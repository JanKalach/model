<?php

declare(strict_types=1);

namespace Leo\Bridges\Database;

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
}
