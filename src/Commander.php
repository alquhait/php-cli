<?php namespace CommandMan\src;

class Commander
{
    const VERSION = '1.1';
    private $command;
    private $callback;
    private $commands = [];
    private $argcount;
    private $argvar;
    private $tmpargs;
    private $defaultCommand = null;
    private static $instance;

    private function __construct()
    {
        $this->initArgs();
        if ($this->argcount === 1) {
            $this->defaultCommand = 'help';
        }
    }

    private function __clone() {}
    private function __wakeup() {}

    public static function getInstance()
    {
        if (!isset(static::$instance)) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    private function initArgs()
    {
        $this->argcount = $_SERVER['argc'];
        $this->argvar = $_SERVER['argv'];
        $this->tmpargs = $_SERVER['argv'];
        $this->tmpargs = array_slice($this->tmpargs, 2);
    }

    private function addCommandFromCallback($name = '' , $description = '')
    {
        $this->appendCommand();
        $this->command = new Command($name, $description);
        return $this;
    }

    public function __call($name, $param)
    {
        if ($name === 'addCommand' && $param[0] instanceof Command) {
            $this->addCommandFromClass($param[0]);
        } elseif ($name === 'addCommand' && is_string($param[0]) && is_string($param[1])) {
            $this->addCommandFromCallback($param[0], $param[1]);
        }
        return $this;
    }


    private function addCommandFromClass($commandClass = null)
    {
        $this->appendCommand();
        $this->command = $commandClass;
        return $this;
    }

    public function run()
    {
        $this->appendCommand();
        if ($this->defaultCommand === 'help') {
            $this->help($this->commands);
        } else {
            $this->_runCommands();
        }
    }

    public function addArgument($argument, $description = '')
    {
        if (!$this->command->getArgument($argument) && $this->isArgumentFound()) {
            $argumentVal = $this->appendVal();
            if ($this->command->isArgument($argumentVal)) {
                $this->command->addArgument($argument, $argumentVal, $description);
            }
        }
        return $this;
    }


    public function addOption($option, $value, $description = '')
    {
        if (!$this->command->getOption($option) && $this->isArgumentFound()) {
            $optionVal = $this->appendVal();
            if ($this->command->isOption($optionVal) && $optionVal == $value) {
                $this->command->addOption($option, $optionVal, $description);
            }
        }
        return $this;
    }
    private function isArgumentFound()
    {
        if (isset($this->argvar[1])) {
            return $this->argvar[1] == $this->command->getName();
        }
        return false;
    }
    private function appendVal()
    {
        if ($this->tmpargs) {
            $val = $this->tmpargs[0];
            array_shift($this->tmpargs);
            return $val;
        }
        return null;
    }

    public function addAction($callback)
    {
        if ($callback instanceof \Closure) {
            $this->callback = $callback;
        }
        return $this;
    }

    private function appendCommand()
    {
        if ($this->command !== null) {
            if ($this->callback instanceof \Closure) {
                $this->commands[] = [$this->command , $this->callback];
            } else {
                $this->commands[] = $this->command;
            }
            $this->callback = null;
            $this->command = null;
        }
    }

    private function _runCommands()
    {
        foreach ($this->commands as $command) {
            if (is_array($command)) {
                if ($command[0]->getName() === $this->argvar[1]) {
                    call_user_func($command[1], $command[0]);
                    return;
                }
            } else {
                if ($command->getName() === $this->argvar[1]) {
                    $command->config(array_slice($this->argvar, 2));
                    $command->addAction();
                    return;
                }
            }
        }
        Error::showError( $this->argvar[1] . ': command not found', Error::ERROR_LEVEL);
    }

    public function help($commands)
    {
        $intro  = "Command Man\n";
        $intro .= "This is simple command line app. \n";
        $intro .= "Usage: command [options] [arguments] \n";
        $intro .= "Command Man version " . static::VERSION . "\n";
        $intro .= "commands: ";
        Output::writeln($intro);
        foreach ($commands as $command) {
            if (is_array($command)) {
                $info['name'] = $command[0]->getName();
                $info['description'] = $command[0]->getDescription();
            } else {
                $info['name'] = $command->getName();
                $info['description'] = $command->getDescription();
            }
            Output::writeln(str_pad($info['name'], 20, ' ') . $info['description']);
        }
    }
}
