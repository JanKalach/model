<?php declare(strict_types=1);

namespace Leo\Model\Types;

use Leo\Model\Interface\ConfigType;

final class FloatType extends BaseType implements ConfigType
{
    public function getType(): string
    {
        return 'double';
    }

    public function isType(): bool
    {
        return is_double($this->value);
    }

    public function toString(): string
    {
        return strval($this->value);
    }

    public function from(string $value): float
    {
        return doubleval($value);
    }
}
