<?php

declare(strict_types=1);

namespace Leo\Bridges\Database;

use Leo\Model\DataType;

final class MySqlTypeMapper implements TypeMapper
{
    public function checkType(array $column): string
    {
        return match ($column['nativetype']) {
            'BIT' => DataType::Boolean,
            'DATE' => DataType::Date,
            'DATETIME', 'TIMESTAMP' => DataType::DateTime,
            'DECIMAL', 'DOUBLE', 'DOUBLE UNSIGNED', 'DOUBLE SIGNED', 'FLOAT' => DataType::Float,
            'LONGTEXT' => DataType::Array,
            'SMALLINT', 'MEDIUMINT', 'INT', 'BIGINT', 'YEAR' => DataType::Int,
            'TIME' => DataType::Time,
            'TINYINT' => $column['size'] === 1 ? DataType::Boolean : DataType::Int,
            default => DataType::String,
        };
    }

    public function allowNull(array $column): bool
    {
        return $column['vendor']['null'] === 'YES';
    }
}
