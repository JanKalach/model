<?php

declare(strict_types=1);

namespace Leo\Bridges\Database;

interface TypeMapper
{
    public function checkType(array $column): string;

    public function allowNull(array $column): bool;
}
