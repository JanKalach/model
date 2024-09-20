<?php

declare(strict_types=1);

namespace Leo;

use Leo\Exception\InvalidModelException;
use Leo\Model\Model;
use Leo\Model\DataType;
use Leo\Model\DataTypes;
use Leo\Model\FetchFul\FetchFulOne;
use Leo\Model\Interface\CaseConvertor;
use Nette;
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
    private ArrayHash $mapping;

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

    public function setMapping(\stdClass $mapping): static
    {
        $this->mapping = new ArrayHash();
        foreach ($mapping as $module => $mask) {
            if (is_string($mask)) {
                if (!preg_match('#^\\\\?([\w\\\\]*\\\\)?(\w*\*\w*?\\\\)?([\w\\\\]*\*\*?\w*)$#D', $mask, $m)) {
                    throw new Nette\InvalidStateException("Invalid mapping mask '$mask'.");
                }

                $this->mapping[$module] = $mask;
            } elseif (is_array($mask) && count($mask) === 3) {
                $this->mapping[$module] = join('', [$mask[0] ? $mask[0] . '\\' : '', $mask[1] . '\\', $mask[2]]);
            } else {
                throw new Nette\InvalidStateException("Invalid mapping mask for module $module.");
            }

            $this->mapping[$module] = str_replace(['*', '\\'], ['(.+)?', '\\\\'], $this->mapping[$module]);
        }

        return $this;
    }

    public function getClassName(string $model, string $from, string $to): string
    {
        if (!isset($this->mapping[$from]) || !isset($this->mapping[$to])) {
            throw new Nette\InvalidStateException("Mapping '$from' or '$to' does not exist.");
        }
        $pattern = '#' . $this->mapping[$from] . '$#';
        $iterator = 0;
        $replace = preg_replace_callback('#\(\.\+\)\?#', function () use ($iterator) {
            $iterator++;
            return '$' . $iterator;
        }, $this->mapping[$to]);

        return preg_replace($pattern, $replace, $model);
    }

    private function generateDataTypes(): DataTypes
    {
        $typeChecker = match (get_class($this->explorer->getConnection()->getDriver())) {
            Drivers\MySqlDriver::class => new Bridges\Database\MySqlTypeMapper(),
            default => new Bridges\Database\StringTypeMapper()
        };

        $dataTypes = new DataTypes();

        foreach ($this->explorer->getStructure()->getTables() as $table) {
            $columns = [];
            foreach ($this->explorer->getStructure()->getColumns($table['name']) as $column) {
                $columnName = $this->caseConvertor->convert($column['name']);
                $columns[$columnName] = new DataType($column, $typeChecker);
            }

            $primaryKey = $this->explorer->getStructure()->getPrimaryKey($table['name']);

            $dataTypes[$table['name']] = [
                'columns' => $columns,
                'primary' => is_array($primaryKey) ? $primaryKey : [$primaryKey],
            ];
        }
        return $dataTypes;
    }

    /**
     * @template T className
     * @param string<T> $model
     * @return T
     */
    public function createModel(string $model, FetchFulOne $fetchFul)
    {
        InjectExtension::callInjects($this->container, $fetchFul);

//        \Tracy\Debugger::barDump($this->getClassName('App\\Model\\ModuleName\\NameOfCollection', 'collection', 'model'));
//        \Tracy\Debugger::barDump($this->getClassName('App\\Model\\NameOfCollection', 'collection', 'model'));
        $class = new $model;

        $class::$dbTable = $class::$dbTable
            ?? $fetchFul->getTable()
            ?? strtolower(str_replace('\\', '_', preg_replace(
                '#' . $this->mapping['model'] . '$#',
                '$1',
                $model
            )))
        ;
        \Tracy\Debugger::barDump($class::$dbTable);
        $fetch = $fetchFul
            ->setTable($class::$dbTable)
            ->fetchOne()
        ;
        return $this->getModel($class, $fetch);
    }

    private function getModel(Model $model, iterable $data)
    {
        foreach ($data as $column => $value) {
            $model->{$this->caseConvertor->convert($column)} = $value;
        }
        return $model;
    }
}
