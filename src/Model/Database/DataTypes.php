<?php declare(strict_types=1);

namespace Leo\Model\Database;

use Nette\Utils\ArrayHash;

/**
 * @internal This is part of leo/factories
 */
class DataTypes extends ArrayHash
{

    public function getDataType(string $table, string $column): DataType
    {
        return $this->$table['columns'][$column];
    }
}
