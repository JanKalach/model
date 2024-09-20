<?php

declare(strict_types=1);

namespace Leo\Model\CaseConvertor;

use Leo\Model\Interface\CaseConvertor;

trait ReverseTrait
{
    private CaseConvertor $reverse;

    public function reverse(string $string): string
    {
        return $this->reverse->convert($string);
    }
}
