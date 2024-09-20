<?php declare(strict_types=1);

namespace Leo\Model\CaseConvertor;

use Leo\Model\Interface\CaseConvertor;

final class SnakeToPascal implements CaseConvertor
{
    use ReverseTrait;

    public function convert(string $string): string
    {
        $words = explode('_', $string);
        return implode('', array_map('ucfirst', $words));
    }
}
