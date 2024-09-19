<?php declare(strict_types=1);

namespace Leo\Model\Types;

use Leo\Model\Interface\ConfigType;

final class IntegerType extends BaseType implements ConfigType
{

    protected mixed $default;

    public function getType(): string
    {
        return 'integer';
    }

    public function isType(mixed $value = null): bool
    {
        return is_int($value ?? $this->value);
    }

    public function toString(): string
    {
        return strval($this->value);
    }

    public function from(string $value): int
    {
        return (int) $value;
    }
}
