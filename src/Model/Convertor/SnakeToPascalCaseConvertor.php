<?php declare(strict_types=1);

namespace Leo\Model\Convertor;

use Leo\Model\Interface\CaseConvertor;

final class SnakeToPascalCaseConvertor implements CaseConvertor
{
    use CaseConvertorTrait;

    public function injectReverse(): void
    {
        $this->reverse = new PascalToSnakeCaseConvertor();
    }

    public function convert(string $string): string
    {
        $words = explode('_', $string);
        return implode('', array_map('ucfirst', $words));
    }
}
