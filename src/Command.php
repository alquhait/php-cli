<?php namespace CommandMan\src;

class Command
{
    protected $name;
    protected $description;
    public $arguments = [];
    protected $options = [];

    public function __construct($name, $description)
    {
        $this->name = $name;
        $this->description = $description;
    }

    public function config($args = [])
    {
        #pass
    }

    public function addAction()
    {
        Error::showError("This command has no action");
        #pass
    }

    protected function setCommandInfo($name, $description)
    {
        $this->name = $name;
        $this->description = $description;
        return $this;
    }

    public function getArgument($argument)
    {
        if (array_key_exists($this->name, $this->arguments)) {
            if (array_key_exists($argument, $this->arguments[$this->name])) {
                return $this->arguments[$this->name][$argument];
            }
        }
        return false;
    }

    public function addArgument($argument, $val, $description = '')
    {
        if (!array_key_exists($this->name, $this->arguments))
            $this->arguments[$this->name] = [];
        $this->arguments[$this->name][$argument] = [$val, $description];
        return $this;
    }

    public function addOption($option, $val, $description = '')
    {
        if (!array_key_exists($this->name, $this->options))
            $this->options[$this->name] = [];

        $this->options[$this->name][$option] = [$val, $description];
        return $this;
    }

    public function getOption($option)
    {
        if (array_key_exists($this->name, $this->options))
            if (array_key_exists($option, $this->options[$this->name])) {
                return $this->options[$this->name][$option];
            }
        return false;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function isArgument($argument)
    {
        return substr($argument, 0, 1) != '-' && substr($argument, 1, 1) != '-';
    }

    public function isOption($option)
    {
        return substr($option, 0, 1) == '-' && substr($option, 1, 1) != '-';
    }
}
