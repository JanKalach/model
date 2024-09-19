<?php declare(strict_types=1);

namespace Leo\Model\Convertor;

use Leo\Model\Interface\CaseConvertor;

class SnakeToCamelCaseConvertor implements CaseConvertor
{
    use CaseConvertorTrait;

    public function injectReverse(): void
    {
        $this->reverse = new CamelToSnakeCaseConvertor();
    }

    public function convert(string $string): string
    {
        $words = explode('_', $string);
        $camelCaseWords = array_map('ucfirst', $words);
        $camelCaseString = implode('', $camelCaseWords);
        return lcfirst($camelCaseString);
    }

}
