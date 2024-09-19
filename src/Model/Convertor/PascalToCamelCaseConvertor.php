<?php declare(strict_types=1);

namespace Leo\Model\Convertor;

use Leo\Model\Interface\CaseConvertor;

final class PascalToCamelCaseConvertor implements CaseConvertor
{
    use CaseConvertorTrait;

    public function injectReverse(): void
    {
        $this->reverse = new CamelToPascalCaseConvertor();
    }

    public function convert(string $string): string
    {
        return lcfirst($string);
    }
}
