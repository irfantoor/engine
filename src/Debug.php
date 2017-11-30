<?php

namespace IrfanTOOR;

class Debug
{
    protected static
        $terminal = 0,
        $abort    = false,
        $level    = 0;

    static function enable($level = 1) {
        self::$level = $level;
        if ($level) {
            if ($level>2)
                error_reporting(E_ALL);
            else
                error_reporting(E_ALL && ~E_NOTICE);

            set_exception_handler(function($obj){
                self::exceptionHandler($obj);
            });

            self::$terminal = isset($_SERVER['TERM']) || isset($_SERVER['TERM_PROGRAM']) || isset($_SERVER['SHELL']);
        } else {
            error_reporting(0);
        }
    }

    static function exceptionHandler($e) {
        self::$abort = true;
        if (!self::level())
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

        $body =
        '<div style="border-left:4px solid #d00; padding:6px;">' .
            '<div style="color:#d00">' . $class . ': ' . $type . $message . '</div><code style="color:#999">file: ' .
            $file . ', line: ' . $line .
            '</code></div>';

        echo $body;
        self::trace($trace);
    }

    static function level()
    {
        return self::$level;
    }

    static function limitPath($file)
    {
        $x = explode('/', $file);
        $l = count($x);
        return ($l>1) ? $x[$l-2] . '/' . $x[$l-1] : $file;
    }

    static function dump($v, $trace=true) {
        if (self::$level == 0)
            return;

        self::writeln($v, 'color:blue');
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

                $ftag  = ($class != '') ? $class . '=>' . $func . '()' : $func . '()';
                self::writeln( '-- file: ' . $file . ', line: ' . $line . ', ' . $ftag, 'color:#999');
            }
        }
    }

    static function banner()
    {
        if (self::$abort)
            return;

        if (self::$level) {
            self::writeln('');
            $t  = microtime(true) - $_SERVER['REQUEST_TIME_FLOAT'];
            $te = sprintf(' %.2f mili sec.', $t * 1000);
            # self::dump('elapsed time: ' . $te, 0);
            self::table(['Elapsed time: ' . $te]);
        }

        if (self::$level > 1) {
            $files = get_included_files();
            foreach ($files as $k=>$file) {
                $list[] = [$k+1, str_replace(ROOT, '', $file)];
            }
            self::table($list, ['No','Shortened path'], 'Files');
        }
    }

    static function table($data, $headers=null, $title=null) {
        if (!is_array($data)) {
            print_r($data);
            return;
        }

        if ($title)
            echo '<br><code><strong>'.$title.'</strong></code>';

        echo '<code><table style="border:1px solid #ccc">';

        if ($headers) {
            echo '<tr style="background-color:#d00;color:#fff;">';
            if (!is_array($headers))
                $headers = [$headers];
            foreach($headers as $header) {
                echo "<th>$header</th>";
            }
            echo '</tr>';
        }

        if (!is_array($data))
            $data = [$data];

        foreach($data as $r=>$row) {
            echo '<tr style="background:#eef">';
            if (!is_array($row))
                $row = [$row];

            if (!is_int($r))
                echo '<td>'.$r.'</td>';

            foreach($row as $item) {
                     echo '<td>';
                     self::table($item);
                     echo '</td>';
            }
            echo '</tr>';
        }
        echo '</table></code>';
    }

    static function write($txt, $styles = null) {
        if (self::$terminal) {
            print_r($txt);
            return;
        }

        $pre = $post = "";
        if ($styles) {
            if (!is_array($styles)) {
                $styles = [$styles];
            }
            $sep = $class = "";
            foreach($styles as $style) {
                $class .= $sep . $style;
                $sep = "; ";
            }
            $pre = '<code style="'.$class.'">';
            $post = "</code>";
        }
        if (!is_string($txt)) {
            if ($styles) {
                $pre = str_replace('code', 'pre', $pre);
                $post = str_replace('code', 'pre', $post);
            } else {
                $pre = '<pre>';
                $post = '</pre>';
            }
            $txt = preg_replace('/\[(.*)\]/U', '[<span style="color:#d00">$1</span>]', print_r($txt, 1));
        }
        echo $pre;
        print_r($txt);
        echo $post;
    }

    static function writeln($txt, $styles=null)
    {
        self::write($txt, $styles);
        if (is_string($txt))
            echo (self::$terminal ? PHP_EOL : '<br />');
    }
}
