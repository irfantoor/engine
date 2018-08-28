#!/usr/bin/env php
<?php

require 'autoload.php';

use IrfanTOOR\Console\Command;
use IrfanTOOR\Console\ShellCommand;

/*
    Commands can be executed on console as:
    e.g. date:
    ./command.php test:date
    
    e.g. dump
    ./command.php test:dump "hello world!" its a test
    
*/
class Test extends Command
{
    function __construct()
    {
        $this->help = [
            'Test Command',
            'test:dump' => 'Dumps the passed arguments',
            'test:date' => 'Displays the current date',
        ];
        
        parent::__construct();
    }
    
    function test_dump($args)
    {
        parent::_dump($args);
    }
    
    function test_date()
    {
        $cmd = new ShellCommand();
        $cmd->execute('date');
        $this->writeln($cmd->output());
    }
}

$cmd = new Test;
$cmd->run();
