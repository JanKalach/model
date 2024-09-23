<?php

declare(strict_types=1);

namespace Leo\Bridges\Database;

use Leo\Model\Database\DataType;

final class StringTypeMapper implements TypeMapper
{
    public function checkType(array $column): string
    {
        return 'string';
    }

    public function allowNull(array $column): bool
    {
        return true;
    }

    public function mapValue(mixed $value, DataType $dataType = null): mixed
    {
        return strval($value) ?? null;
    }
}
