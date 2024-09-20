<?php declare(strict_types=1);

namespace Leo\Model\CaseConvertor;

use Leo\Model\Interface\CaseConvertor;

class NoChange implements CaseConvertor
{
    public function convert(string $string): string
    {
        return $string;
    }

    public function reverse(string $string): string
    {
        return $string;
    }
}
