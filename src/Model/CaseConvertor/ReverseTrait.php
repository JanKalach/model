<?php

declare(strict_types=1);

namespace Leo\Model\CaseConvertor;

use Leo\Model\Interface\CaseConvertor;

trait ReverseTrait
{
    private CaseConvertor $reverse;

    public function injectReverse(): void
    {
        $class = get_called_class();
        $split = explode('\\', $class);
        $last = array_pop($split);
        $split[] = join('To', array_reverse(explode('To', $last)));
        $class = implode('\\', $split);
        $this->reverse = new $class();

    }

    public function reverse(string $string): string
    {
        return $this->reverse->convert($string);
    }
}
