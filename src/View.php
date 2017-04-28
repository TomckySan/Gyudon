<?php

class View
{
    public static function puts($msg)
    {
        echo $msg.PHP_EOL.PHP_EOL;
    }

    public static function color($txt, $code = '39')
    {
        return "\033[0;{$code}m".$txt."\033[0m";
    }
}
