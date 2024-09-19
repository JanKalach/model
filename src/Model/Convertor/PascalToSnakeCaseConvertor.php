<?php declare(strict_types=1);

namespace Leo\Model\Convertor;

use Leo\Model\Interface\CaseConvertor;

final class PascalToSnakeCaseConvertor implements CaseConvertor
{
    use CaseConvertorTrait;

    public function injectReverse(): void
    {
        $this->reverse = new SnakeToPascalCaseConvertor();
    }

    public function convert(string $string): string
    {
        return strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $string));
    }
}
