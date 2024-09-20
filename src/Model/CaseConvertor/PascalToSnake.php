<?php declare(strict_types=1);

namespace Leo\Model\CaseConvertor;

use Leo\Model\Interface\CaseConvertor;

final class PascalToSnake implements CaseConvertor
{
    use ReverseTrait;

    public function convert(string $string): string
    {
        return strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $string));
    }
}
