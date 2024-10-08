<?php declare(strict_types=1);

namespace Leo\Model\CaseConvertor;

use Leo\Model\Interface\CaseConvertor;

class CamelToPascal implements CaseConvertor
{
    use ReverseTrait;

    public function convert(string $string): string
    {
        return ucfirst($string);
    }
}
