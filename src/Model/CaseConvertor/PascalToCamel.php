<?php declare(strict_types=1);

namespace Leo\Model\CaseConvertor;

use Leo\Model\Interface\CaseConvertor;

final class PascalToCamel implements CaseConvertor
{
    use ReverseTrait;

    public function injectReverse(): void
    {
        $this->reverse = new CamelToPascal();
    }

    public function convert(string $string): string
    {
        return lcfirst($string);
    }
}
