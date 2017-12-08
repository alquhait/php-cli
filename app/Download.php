<?php namespace App;

use CommandMan\src\Command;
use CommandMan\src\Output;
use CommandMan\src\Error;

class Download extends Command
{
    public function __construct($name = 'download', $description = '')
    {
        $this->setCommandInfo($name, $description);
    }

    public function config($args = [])
    {
        if (count($args) == 2) {
            if ($this->isArgument($args[0])) {
                $this->addArgument('from', $args[0], 'url to get file from');
            }
            if ($this->isArgument($args[1])) {
                $this->addArgument('to', $args[1], 'local path to save file to');
            }
        } elseif (count($args) == 1) {
            if ($this->isOption($args[0]) && $args[0] === '-h') {
                $this->addOption('help', '-h', 'list all arguments and options');
            }
        }
    }

    public function addAction()
    {
        $from = $this->getArgument('from')[0];
        $to = $this->getArgument('to')[0];
        $help = $this->getOption('help')[0];
        if ($from != false && $to != false) {
            $this->download($from, $to);
            Output::writeln('Download complete.', $fcolor='white', $bcolor='green');
        } elseif ($help == '-h') {
            Output::writeln('help people');
        } else {
            Error::showError('Usage: download url filename');
        }
    }

    public function download($from, $to)
    {
        Output::writeln( "\n" . 'Get File from: ' . $from);
        Output::writeln('Downloading...');
        file_put_contents($to, fopen($from, 'r'));
        Output::writeln('File saved: ' . $to);
    }
}