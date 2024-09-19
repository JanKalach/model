<?php declare(strict_types=1);

namespace Leo\Model\Types;

use Leo\Model\Interface\ConfigType;

final class ArrayType extends BaseType implements ConfigType
{
    public function getType(): string
    {
        return 'array';
    }

    public function isType(mixed $value = null): bool
    {
        return is_array($value ?? $this->value);
    }

    public function toString(): string
    {
        return serialize($this->value);
    }

    public function from(string $value): array
    {
        return unserialize($value);
    }
}
