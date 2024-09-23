<?php

declare(strict_types=1);

namespace Leo\Bridges\Database;

use Leo\Model\DataType;
use Nette\Utils\ArrayHash;
use Nette\Utils\Json;

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

    public function mapValue(mixed $value, DataType $dataType = null): mixed
    {
        if ($dataType === null) {
            return $value;
        }
        return match ($dataType->getType()) {
            DataType::Int => intval($value),
            DataType::String, DataType::Text => trim(strval($value)),
            DataType::Float => floatval($value),
            DataType::Boolean => boolval($value),
            DataType::Date, DataType::DateTime => DateTime::from($value),
            DataType::Time => ($value instanceof \DateInterval) ? $value : \DateInterval::createFromDateString($value),
            DataType::Array => $this->getArrayValue($value),
            default => throw new \RuntimeException('The specified "' . $dataType->getName() . '" column has no defined type "' . $dataType->getType() . '"')
        };
    }

    public function saveValue(mixed $value, DataType $dataType): mixed
    {
        if ($dataType->getType() === DataType::Array) {
            return Json::encode($value) ?? [];
        }
        return $value;
    }

    private function getArrayValue(mixed $value): mixed
    {
        if (is_string($value)) {
            $json = Json::decode($value, forceArrays: JSON_OBJECT_AS_ARRAY);
            return (json_last_error() === JSON_ERROR_NONE && is_array($json))
                ? $json
                : [$value];
        } elseif (is_array($value)
            || $value instanceof ArrayHash
        ) {
            return $value;
        } elseif (is_iterable($value)) {
            return (array)$value;
        }
        return [];
    }

}
