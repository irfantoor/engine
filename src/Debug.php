<?php

namespace IrfanTOOR\Engine;

use IrfanTOOR\Console;

/**
 * Debugging while developement
 */
class Debug
{
    protected static
        $error    = 0,
        $level    = 0,
        $terminal = 0;

    static function enable($level = 1)
    {
        if (isset($_SERVER['TERM']))
            self::$terminal = new Console();

        self::$level = $level;
        register_shutdown_function(['\IrfanTOOR\Engine\Debug', 'shutdown']);
        if ($level < 3)
            ob_start();

        if ($level>2)
            error_reporting(E_ALL);
        elseif($level)
            error_reporting(E_ALL && ~E_NOTICE);
        else
            error_reporting(0);

        set_exception_handler(function($obj){
            self::exceptionHandler($obj);
        });
    }

    static function level()
    {
        return static::$level;
    }

    static function dump($var, $trace=1)
    {
        if (!self::$level)
            return;

        if (self::$terminal) {
            self::$terminal->writeln(print_r($var, 1), 'light_cyan');
        } else {
            $txt = preg_replace('/\[(.*)\]/u', '[<span style="color:#d00">$1</span>]', print_r($var, 1));
            echo '<pre style="color:blue">' . $txt . "</pre>";
        }

        if ($trace)
            self::trace();
    }

    static function trace($trace=null)
    {
        $trace = $trace ?: debug_backtrace();
        foreach( $trace as $er) {
            $func = isset($er['function'])? $er['function']: '';
            $file = isset($er['file']) ? $er['file'] : '';

            # last two sections of the path
            if ($file) {
                $file  = self::limitPath($file);
                $line  = isset($er['line'])? $er['line']: '';
                $class = isset($er['class'])? $er['class']: '';
                if ($class == 'IrfanTOOR\Debug' && $func=='trace')
                    continue;

                $ftag = ($class != '') ? $class . '=>' . $func . '()' : $func . '()';
                $txt = '-- file: ' . $file . ', line: ' . $line . ', ' . $ftag;
                if (self::$terminal)
                    self::$terminal->writeln( $txt, 'color_111');
                else
                    echo '<code style="color:#999">' . $txt . '</code><br>';
            }
        }
    }

    static function limitPath($file)
    {
        $x = explode('/', $file);
        $l = count($x);
        return ($l>1) ? $x[$l-2] . '/' . $x[$l-1] : $file;
    }

    static function exceptionHandler($e) {
        ob_get_clean();

        self::$error = true;

        if (!self::$level)
            return;

        if (is_object($e)) {
            $class   = 'Exception';
            $message = $e->getMessage();
            $file    = self::limitPath($e->getFile());
            $line    = $e->getLine();
            $type    = '';
            $trace   = $e->getTrace();
        }
        else {
            $class = 'Error';
            extract($e);
            $type .= ' - ';
        }

        if (self::$terminal) {
            self::$terminal->writeln([$class . ': ' . $type . $message], ['white','bg_red']);
            self::$terminal->writeln('file ' . $file . ', line: ' . $line , 'cyan');
        } else {
            $body =
            '<div style="border-left:4px solid #d00; padding:6px;">' .
                '<div style="color:#d00">' . $class . ': ' . $type . $message . '</div><code style="color:#999">file: ' .
                $file . ', line: ' . $line .
                '</code></div>';

            echo $body;
        }
        self::trace($trace);
    }

    static function shutdown()
    {
        if (self::$error)
            return;

        if (ob_get_level() > 0)
            ob_get_flush();

        if (self::$level) {
            echo (self::$terminal ? PHP_EOL : '<br>');
            $t  = microtime(true) - $_SERVER['REQUEST_TIME_FLOAT'];
            $te = sprintf(' %.2f mili sec.', $t * 1000);
            # self::dump('elapsed time: ' . $te, 0);
            self::dump('Elapsed time: ' . $te, 0);
        }

        if (self::$level > 1) {
            $files = get_included_files();
            self::dump($files, 0);
        }
    }
}
