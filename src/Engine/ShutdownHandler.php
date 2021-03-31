<?php

namespace IrfanTOOR\Engine;

use Exception;
use IrfanTOOR\Engine;
use IrfanTOOR\Engine\RequestHandlerInterface;
use IrfanTOOR\Terminal;
use IrfanTOOR\Http\Response;
use Psr\Http\Message\{ServerRequestInterface, ResponseInterface};
use Throwable;

# Handles a premature shutdown of Engine caused by some error or by dd() ...
# and prints the error or output in a readable format
class ShutdownHandler implements RequestHandlerInterface
{
    /** @var Engine */
    protected $engine;

    /** @var Terminal */
    protected $terminal;

    function __construct($engine = null)
    {
        $this->engine = $engine ?? new Class() {
            const NAME    = Engine::NAME;
            const VERSION = Engine::VERSION;
        };
    }

    function getTerminal()
    {
        if (!$this->terminal)
            $this->terminal = new Terminal();

        return $this->terminal;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $contents = $request->getAttribute('contents', '');
        $status = $request->getAttribute('status', Engine::STATUS_ERROR);

        if ($contents === '') {
            $t = $this->getTerminal();

            ob_start();
            $t->write("| ", "light_red, bold");
            $t->writeln("Nothing to display ...", "light_red");
            $t->writeln("  Increase the debug level to view the details", "info");
            $contents = ob_get_clean();
        }

        switch ($status) {
            case Engine::STATUS_FATAL_ERROR:
            case Engine::STATUS_ERROR:
                $title  = "Error";
                break;

            case Engine::STATUS_EXCEPTION:
                $title = "Exception";
                break;

            case Engine::STATUS_TRANSIT:
            case Engine::STATUS_OK:
            default:
                $title  = "Shutdown Handler";
        }

        $data = [
            'title'   => $title,
            'engine'  => $this->engine::NAME,
            'version' => $this->engine::VERSION,
        ];

        $tplt     = $this->template($data);
        $tplt     = str_replace('{$contents}', $contents, $tplt);

        # if the provider is not aavailable ...
        try {
            $response = $this->engine->create('Response');
        } catch (Throwable $th) {
            $response = new Response();
        }

        if (Engine::STATUS_OK !== $status)
            $response = $response->withStatus(500);

        $response->getBody()->write($tplt);
        return $response;
    }

    # Returns a simple http page template, with a provided title
    public function template(array $data)
    {
        extract($data);

        # cli template
        if (PHP_SAPI === 'cli') {
            $t = $this->getTerminal();
            $t->ob_start();
            $t->writeln(" " . $title . " ", "black, bg_white");
            $t->writeln();
            $t->writeln('{$contents}');
            $t->writeln("{$engine} v{$version}", "dark");

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
    <p>{$engine} v{$version}</p>
</body>
</html>
END;
        }

        return $tplt;
    }
}
