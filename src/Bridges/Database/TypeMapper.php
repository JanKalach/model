<?php

declare(strict_types=1);

namespace Leo\Bridges\Database;

use Leo\Model\DataType;

interface TypeMapper
{
    public function checkType(array $column): string;

    public function allowNull(array $column): bool;

    public function mapValue(mixed $value, DataType $dataType = null): mixed;
}
