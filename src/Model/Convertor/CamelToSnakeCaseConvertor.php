<?php declare(strict_types=1);

namespace Leo\Model\Convertor;

use Leo\Model\Interface\CaseConvertor;

class CamelToSnakeCaseConvertor implements CaseConvertor
{
    use CaseConvertorTrait;

    public function injectRevertConvertor(): void
    {
        $this->reverse = new CamelToSnakeCaseConvertor();
    }

    public function convert(string $string): string
    {
        return strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $string));
    }

}
