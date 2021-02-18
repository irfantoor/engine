<?php

namespace IrfanTOOR\Engine;

use IrfanTOOR\{Debug, Terminal};

# Handles a thrown Exception, by printing it in a readable format
class ExceptionHandler
{
    public function handle($e)
    {
        $t = new Terminal();

        $dl = Debug::getLevel();

        if ($dl === 0) {
            $t->write("| ", "light_red, bold");
            $t->writeln("Encounterd an Exception or a Fatal error", "light_red");
            $t->writeln("  Increase the debug level to view the details", "info");
        } else {
            $t->write("| ", "light_red, bold");
            $t->writeln($e->getMessage(), "light_red");
        }

        if ($dl > 1) {
            $t->writeln("  file: " . $e->getFile() . ", line: " . $e->getLine(), "info");
        }

        if ($dl > 2) {
            foreach ($e->getTrace() as $tr) {
                if (isset($tr['file'])) {
                    $t->writeln(
                        "  file: " . $tr['file'] . ", line: " . $tr['line'] .
                        (
                            isset($tr['class'])
                            ? ", class: " . $tr['class']
                            : "") .
                        (
                            isset($tr['function'])
                            ? ", function: " . $tr['function']
                            : ""
                        ),

                        "dark_gray"
                    );
                }
            };
        }
    }
}
