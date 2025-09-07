<?php

declare(strict_types=1);

namespace bultonFr\Utils\Cli;

class BasicMsg
{
    public static function displayMsg(
        string $msg,
        string $txtColor = 'white',
        string $txtStyle = 'normal'
    ) {
        $nbArgs = func_num_args();

        if ($nbArgs === 1) {
            echo $msg;
            static::flush();

            return;
        }

        $txtStyleCode = static::obtainTxtStyleCode($txtStyle);
        $txtColorCode = static::obtainTxtColorCode($txtColor);

        echo "\033[" . $txtStyleCode . ";" . $txtColorCode . "m" . $msg . "\033[0m";
        static::flush();
    }

    public static function displayMsgNL(
        string $msg,
        string $txtColor = 'white',
        string $txtStyle = 'normal'
    ) {
        $nbArgs = func_num_args();
        if ($nbArgs === 1) {
            static::displayMsg($msg);
            echo "\n";
            static::flush();
            return;
        }

        static::displayMsg($msg, $txtColor, $txtStyle);
        echo "\n";
        static::flush();
    }

    protected static function flush()
    {
        if (ob_get_status() !== []) {
            ob_flush();
        }
    }

    protected static function obtainTxtColorCode(string $txtColor): int
    {
        if ($txtColor === 'red') {
            return 31;
        } elseif ($txtColor === 'green') {
            return 32;
        } elseif ($txtColor === 'yellow') {
            return 33;
        }

        return 37; //white
    }

    protected static function obtainTxtStyleCode(string $txtStyle): int
    {
        if ($txtStyle === 'bold') {
            return 1;
        }

        return 0; //normal
    }
}
