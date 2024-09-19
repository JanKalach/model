<?php

declare(strict_types=1);

namespace Leo;

use Leo\Model\DataType;
use Leo\Model\DataTypes;
use Leo\Model\Interface\CaseConvertor;
use Nette\Caching;
use Nette\Database\Drivers;
use Nette\Database\Explorer;
use Nette\DI\Container;
use Nette\DI\Extensions\InjectExtension;
use Nette\Utils\ArrayHash;

final class ModelFactory
{
    private Container $container;
    private Caching\Cache $cache;
    private Explorer $explorer;
    private CaseConvertor $caseConvertor;
    private DataTypes $dataTypes;
    private ArrayHash $mapClass;

    public function __construct(
        Explorer $explorer,
        CaseConvertor $caseConvertor,
        Container $container,
        Caching\Storage $storage,

    )
    {
        $this->container = $container;
        $this->explorer = $explorer;
        $this->caseConvertor = $caseConvertor;
        $this->cache = new Caching\Cache($storage, 'models');

        InjectExtension::callInjects($this->container, $this->caseConvertor);

        $this->dataTypes = \Tracy\Debugger::detectDebugMode() === false
            ? $this->cache->load('modelDataTypes', fn () => $this->generateDataTypes())
            : $this->generateDataTypes();

    }

    public function mapClass(string $pattern, string $replace): void
    {
        $this->mapClass = ArrayHash::from([
            'pattern' => $pattern,
            'replace' => $replace
        ]);
    }

    private function generateDataTypes(): DataTypes
    {
        $typeChecker = match (get_class($this->explorer->getConnection()->getDriver())) {
            Drivers\MySqlDriver::class => new Bridges\Database\MySqlTypeMapper(),
            default => new Bridges\Database\StringTypeMapper()
        };

        $dataTypes = new DataTypes();

        foreach ($this->explorer->getStructure()->getTables() as $table) {
            if ($table['view']) {
                continue;
            }
            $columns = [];
            foreach ($this->explorer->getStructure()->getColumns($table['name']) as $column) {
                $columnName = $this->caseConvertor->convert($column['name']);
                $type = $typeChecker->checkType($column);
                $columns[$columnName] = new DataType(
                    name: $column['name'],
                    type: $type,
                    nativeType: $column['nativetype'],
                    default: $column['default'],
                    size: $column['size'],
                    nullable: $column['nullable'],
                    allowNull: ($column['vendor']['notnull'] ?? 0) == 0,
                );
            }
            $dataTypes[$table['name']] = $columns;
        }
        return $dataTypes;
    }

    /**
     * @template T className
     * @param string<T> $model
     * @return T
     */
    public function createModel(string $model)
    {
        $class = new $model;



        return $class;
    }
}
