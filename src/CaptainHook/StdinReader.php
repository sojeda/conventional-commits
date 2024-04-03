<?php

declare(strict_types=1);

namespace Sojeda\CaptainHook;

use function feof;
use function fgets;
use function fopen;

class StdinReader
{
    public function read(): string
    {
        $in = fopen('php://stdin', 'rb');
        $buffer = '';

        if ($in) {
            while (!feof($in)) {
                $buffer .= fgets($in, 4096);
            }
        }

        return $buffer;
    }
}
