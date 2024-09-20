<?php declare(strict_types=1);

namespace Leo\Model\CaseConvertor;

use Leo\Model\Interface\CaseConvertor;

class SnakeToCamel implements CaseConvertor
{
    use ReverseTrait;

    public function injectReverse(): void
    {
        $this->reverse = new CamelToSnake();
    }

    public function convert(string $string): string
    {
        $words = explode('_', $string);
        $camelCaseWords = array_map('ucfirst', $words);
        $camelCaseString = implode('', $camelCaseWords);
        return lcfirst($camelCaseString);
    }

}
