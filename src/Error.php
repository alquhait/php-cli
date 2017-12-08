<?php namespace CommandMan\src;

class Error
{
    const WARNING_LEVEL = 1;
    const ERROR_LEVEL = 2;
    const FATAL_LEVEL = 3;

    public static function showError($message = '', $level = 1)
    {
        $error = '';
        switch($level)
        {
            case 1:
                $error = 'Warning: ';
                $color = 'yellow';
                break;
            case 2:
                $error = 'Error: ';
                $color = 'red';
                break;
            case 3:
                $error = 'Fatal: ';
                $color = 'red';
                break;
            default :
                $error = 'Unknown: ';
                $color = 'yellow';
        }

        Output::writeln($error . $message, 'black', $color);
    }
}