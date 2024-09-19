<?php

declare(strict_types=1);

namespace Leo\Model\Convertor;

use Leo\Model\Interface\CaseConvertor;

trait CaseConvertorTrait
{
    private CaseConvertor $reverse;

    public function reverse(string $string): string
    {
        return $this->reverse->convert($string);
    }
}
