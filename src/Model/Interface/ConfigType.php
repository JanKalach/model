<?php declare(strict_types=1);

namespace Leo\Model\Interface;

interface ConfigType
{
    function getType(): string;

    function isType(): bool;

    function toString(): string;

    function from(string $value);
}
