<?php

namespace IrfanTOOR\Engine\Command;

use IrfanTOOR\Console\Command;
use IrfanTOOR\Engine\Exception;
use IrfanTOOR\Engine\Filesystem;

class Database extends Command
{
    protected $fs = null;

    function __construct()
    {
        $this->help = [
            'Database console',
            'database:backup'   => [
                'backup a database',
                '
FORMAT
------
database:backup dbname|--all

OPTIONS
-------
dbname    name of the database
--all     backup all of the databases, whose model exists
                '
            ],
            'database:config' => 'displays the database config',
            'database:create' => [
                'creates the database',
                '
FORMAT
------
database:create [dbname|--all]

OPTIONS
-------
dbname    name of the database
--all     try creating all of the databases, whose model exists
                '
            ],
            'database:delete' => [
                'deletes a database model',
                '
FORMAT
------
database:delete dbname|--all

OPTIONS
-------
dbname    name of the database
--all     delete all of the databases, whose model exists
                '
            ],
            'database:diff' => [
                'displays the database difference',
                '
FORMAT
------
database:diff dbname [dbname2]

OPTIONS
-------
dbname     name of the database model
dbname2    name of the old database model (diff with the last backup otherwise)
                '
            ],
            'database:info'   => [
                'displays the info of a datbase',
                '
FORMAT
------
database:info dbname

OPTIONS
-------
dbname    name of the database
                '
            ],
            'database:list'   => 'displays the databases',
            'database:query' => [
                'executes a query on a database',
                '
FORMAT
------
database:query dbname "query"

OPTIONS
-------
dbname    name of the database
query     query to be executed
                '
            ],
            'database:schema' => [
                'displays the schema of a database',
                '
FORMAT
------
database:schema dbname

OPTIONS
-------
dbname    name of the database
                '
            ],
        ];
        
        parent::__construct();
    }
    
    function database_backup($args)
    {
        if ($args) {
            if ($args[0] === '--all') {
                $list = $this->getList();
                foreach($list as $model) {
                    $this->backup([$model]);
                }
            } else {
                $model = ucfirst($args[0]);
                try {
                    $cname = 'App\\Model\\' . $model;
                    $class = new $cname;
                    $source = $class->getFile();
                    $source = str_replace(ROOT . 'storage/', '', $source);
                    $target = 'backup/' . $source;

                    $fs = $this->fs = $this->fs ?: new Filesystem(ROOT . 'storage/');

                    if ($fs->has($target)) {
                        $hash1 = md5($fs->read($source));
                        $hash2 = md5($fs->read($target));
                        if ($hash1 === $hash2) {
                            $this->writeln('[ ] ' . $model . ' -- no changes detected in the last backup', 'yellow');
                            return true;
                        } else {
                            $ts = $fs->getTimestamp($target);
                            $fs->rename($target, $target . '.' . $ts);
                            return $this->database_backup($args, $delete_source);
                        }
                    } else {
                        try {
                            $fs->copy($source, $target);
                            $this->writeln('[x] ' . $model . ' -- backup done');
                            return true;
                        } catch (\Exception $e) {
                            $this->writeln('[ ] ' . $model . ' -- ' . $e->getMessage(), 'red');
                            return false;
                        }
                    }
                    # $this->writeln('[ ] ' . $target . ' -- todo', 'yellow');
                } catch (Exception $e) {
                    $msg =  $e->getMessage();
                    $this->writeln('[ ] ' . $model . ' -- ' . $msg , 'red');
                }
            }
        } else {
            $this->help('database:backup');
        }
    } 

    function database_config($args)
    {
        $config = require APP . 'config.php';

        $this->writeln('');
        $this->writeln('database:config', 'bg_blue');
        $this->writeln('');
        $this->_dump($config['database'] ?: [], false);
    }

    function database_diff($args)
    {
        if (!$args) {
            $this->help('database:diff');
            exit;
        }

        $dbname = $args[0];
        $dbname2 = isset($args[1]) ? $args[1] : $dbname;

        $model   = ucfirst($args[0]);
        $cname   = '\\App\\Model\\' . $model;
        $class   = new $cname();
        $file1   = $class->getFile();
        $storage = ROOT . 'storage/';
        $backup  = $storage . 'backup/';
        $file2   = str_replace($storage, $backup, $file1);

        system("sqldiff $file2 $file1 | head");
    }

    function database_info($args)
    {
        if ($args) {
            $model = ucfirst($args[0]);
            $cname = '\\App\\Model\\' . $model;
            $class = new $cname();
            $item = $class->getFirst(
                ['select' => 'count(*)']
            );
            $count = $item[0];
            $this->writeln('Count: ' . $count);

            if ($count) {
                $this->writeln('Last Record');
                $this->_dump(
                    $class->getFirst(
                        [
                            'orderby' => 'id desc',
                        ]
                    )
                );

            }

        } else {
            $this->help('database:info');
        }
    }

    function database_list($args)
    {
        $list = $this->_getList();
        $this->writeln('database list', 'bg_blue');
        $this->writeln('');
        $this->_dump($list);
    }

    function _getList()
    {
        $fs = new Filesystem(ROOT . 'app/model/');
        $list = $fs->listContents();

        $models = [];
        foreach($list as $item) {
            $models[] = $item['filename'];
        }

        return $models;
    }

    function database_schema($args)
    {
        if ($args) {
            $model = ucfirst($args[0]);
            $cname = '\\App\\Model\\' . $model;
            $class = new $cname();

            $this->_dump($class->getSchema(), false);

        } else {
            $this->help('database:schema');
        }
    }

    function database_create($args)
    {
        if ($args) {
            if ($args[0] === '--all') {
                $list = $this->getList();
                foreach($list as $model) {
                    $this->create([$model]);
                }
            } else {
                $model = ucfirst($args[0]);
                try {
                    $cname = 'App\\Model\\' . $model;
                    $class = new $cname;
                    $class->create();
                    $this->writeln('[x] ' . $model . ' -- created');
                } catch (Exception $e) {
                    $msg =  $e->getMessage();
                    if (strpos($msg, 'already exists')) {
                        $this->writeln('[ ] ' . $model . ' -- already exists' , 'yellow');
                    } elseif (strpos($msg, 'does not exist')) {
                        preg_match('/\[(.*)\]/', $msg, $m);
                        if ($m[1]) {
                            $source = $m[1];
                            $source = str_replace(ROOT . 'storage/', '', $source);
                            $fs = $this->fs = $this->fs ?: new Filesystem(ROOT . 'storage/');
                            $fs->write($source, '');
                            $this->create($args);
                        }
                    } else {
                        $this->writeln('[ ] ' . $model . ' -- ' . $msg , 'red');
                    }
                }
            }
        } else {
            $this->help('database:create');
        }
    }

    function database_delete($args)
    {
        if ($args) {
            // ob_get_start();
            if ($args[0] === '--all') {
                $list = $this->getList();
                foreach($list as $model) {
                    $this->delete([$model]);
                }
            } else {
                # backup
                if ($this->database_backup($args))
                {
                    # and delete
                    $model = ucfirst($args[0]);
                    try {
                        $cname = 'App\\Model\\' . $model;
                        $class = new $cname;
                        $source = $class->getFile();
                        $source = str_replace(ROOT . 'storage/', '', $source);
                        $fs = $this->fs;
                        $fs->delete($source);
                        $this->writeln('[x] ' . $model . ' -- deleted');
                        return true;
                    } catch (\Exception $e) {
                        $this->writeln('[ ] ' . $model . ' -- ' . $e->getMessage(), 'red');
                        return false;
                    }
                }
            }
        } else {
            $this->help('database:delete');
        }
    }


    function database_query($args) {
        if (count($args) < 2) {
            $this->help('database:query');
            exit;
        }

        $model   = ucfirst($args[0]);
        $cname   = '\\App\\Model\\' . $model;
        $class   = new $cname();

        $sql = $args[1];
        $result = $class->query(
            $sql
        );

        $this->_dump($result);
    }
}
