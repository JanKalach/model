<?php declare(strict_types=1);

namespace Leo\Model\Types;

use Leo\Model\Interface\ConfigType;

final class StringType extends BaseType implements ConfigType
{
    public function getType(): string
    {
        return 'string';
    }

    public function isType(): bool
    {
        return is_string($this->value);
    }

    public function toString(): string
    {
        return strval($this->value);
    }

    public function from(string $value): string
    {
        return $value;
    }
}
