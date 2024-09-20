<?php declare(strict_types=1);

namespace Leo\Model\CaseConvertor;

use Leo\Model\Interface\CaseConvertor;

class CamelToSnake implements CaseConvertor
{
    use ReverseTrait;

    public function injectRevertConvertor(): void
    {
        $this->reverse = new CamelToSnake();
    }

    public function convert(string $string): string
    {
        return strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $string));
    }

}
