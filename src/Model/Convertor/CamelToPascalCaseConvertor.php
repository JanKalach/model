<?php declare(strict_types=1);

namespace Leo\Model\Convertor;

use Leo\Model\Interface\CaseConvertor;

class CamelToPascalCaseConvertor implements CaseConvertor
{
    use CaseConvertorTrait;

    public function injectReverse(): void
    {
        $this->reverse = new PascalToCamelCaseConvertor();
    }

    public function convert(string $string): string
    {
        return ucfirst($string);
    }
}
