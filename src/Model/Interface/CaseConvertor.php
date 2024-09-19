<?php declare(strict_types=1);

namespace Leo\Model\Interface;

interface CaseConvertor
{
    public function convert(string $string);
    public function reverse(string $string);
}
