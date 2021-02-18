<?php

namespace IrfanTOOR\Engine;

use Exception;
use IrfanTOOR\Terminal;

use IrfanTOOR\Http\Response;

# Handles a premature shutdown of Engine caused by some error or by dd() ...
# and prints the error or output in a readable format
class ShutdownHandler
{
    protected $name;
    protected $version;
    protected $status;

    protected $terminal;

    # todo -- should be defined in a single file
    const STATUS_OK          =  1;
    const STATUS_TRANSIT     =  0;
    const STATUS_EXCEPTION   = -1;
    const STATUS_ERROR       = -2;
    const STATUS_FATAL_ERROR = -3;

    function __construct(string $name, string $version, int $status)
    {
        $this->name    = $name;
        $this->version = $version;
        $this->status  = $status;
    }

    function getTerminal()
    {
        if (!$this->terminal)
            $this->terminal = new Terminal();

        return $this->terminal;
    }

    public function handle($contents = '')
    {
        if ($contents === '') {
            $t = $this->getTerminal();

            ob_start();
            $t->write("| ", "light_red, bold");
            $t->writeln("Nothing to display ...", "light_red");
            $t->writeln("  Increase the debug level to view the details", "info");
            $contents = ob_get_clean();
        }

        switch ($this->status) {
            case self::STATUS_FATAL_ERROR:
            case self::STATUS_ERROR:
                $title  = "Error";
                break;

            case self::STATUS_EXCEPTION:
                $title = "Exception";
                break;

            case self::STATUS_TRANSIT:
            default:
                $title  = "Shutdown Handler";
        }

        $tplt = $this->template($title);
        $tplt = str_replace('{$contents}', $contents, $tplt);

        $response = (new Response())->withStatus(500);
        $response->getBody()->write($tplt);
        return $response;
    }

    # Returns a simple http page template, with a provided title
    public function template(string $title)
    {
        # cli template
        if (PHP_SAPI === 'cli') {
            $t = $this->getTerminal();
            $t->ob_start();
            $t->writeln(" " . $title . " ", "black, bg_white");
            $t->writeln();
            $t->writeln('{$contents}');
            $t->writeln("{$this->name} - v{$this->version}", "dark");

            $tplt = $t->ob_get_contents();
        }

        # html template
        else {
            $tplt = <<<END
<!DOCTYPE html>
<html>
<head>
<title>{$title}</title>
    <style>
        body {font-family: arial; padding: 20px;}
        hr {border: 0; border-top: 1px solid #eee; padding: 6px;}
    </style>
</head>
<body>
    <h1>{$title}</h1>
    <hr>
    <p>{\$contents}</p>
    <hr>
    <p>{$this->name} - v{$this->version}</p>
</body>
</html>
END;
        }

        return $tplt;
    }
}
