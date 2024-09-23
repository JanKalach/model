<?php

declare(strict_types=1);

namespace Leo;

use Leo\Bridges\Database\TypeMapper;
use Leo\Bridges\ModelCache;
use Leo\Model\Database\DataType;
use Leo\Model\Database\DataTypes;
use Leo\Model\FetchFul\FetchFulOne;
use Leo\Model\Interface\CaseConvertor;
use Leo\Model\Model;
use Nette;
use Nette\Database\Explorer;
use Nette\DI\Container;
use Nette\DI\Extensions\InjectExtension;
use Nette\Utils\ArrayHash;

final class ModelFactory
{
    private Container $container;
    private ModelCache $cache;
    private Explorer $explorer;
    private CaseConvertor $caseConvertor;
    private DataTypes $dataTypes;
    private ArrayHash $mapping;
    private TypeMapper $typeMapper;

    private bool $rememberLoadedClasses = true;
    private array $loaded = [];

    public function __construct(
        Explorer $explorer,
        CaseConvertor $caseConvertor,
        Container $container,
        ModelCache $cache,
        TypeMapper $typeMapper,
    )
    {
        $this->container = $container;
        $this->explorer = $explorer;
        $this->caseConvertor = $caseConvertor;
        $this->typeMapper = $typeMapper;
        $this->cache = $cache;

        InjectExtension::callInjects($this->container, $this->caseConvertor);

        $this->dataTypes = \Tracy\Debugger::detectDebugMode() === false
            ? $this->cache->load('modelDataTypes', fn () => $this->generateDataTypes())
            : $this->generateDataTypes();

    }

    public function setMapping(\stdClass $mapping): self
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

    public function setRememberLoadedClasses(bool $rememberLoadedClasses): self
    {
        $this->rememberLoadedClasses = $rememberLoadedClasses;
        return $this;
    }

    public function getExplorer(): Explorer
    {
        return $this->explorer;
    }

    public function getCache(): ModelCache
    {
        return $this->cache;
    }

    protected function getClassName(string $model, string $from, string $to): string
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
        $dataTypes = new DataTypes();

        foreach ($this->explorer->getStructure()->getTables() as $table) {
            $columns = [];
            foreach ($this->explorer->getStructure()->getColumns($table['name']) as $column) {
                $columnName = $this->caseConvertor->convert($column['name']);
                $columns[$columnName] = new DataType($column, $this->typeMapper);
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
     * @template T of Model
     * @param string<T> $model
     * @param FetchFulOne $fetchFul
     * @param bool $useCache
     * @return T
     */
    public function createModel(string $model, FetchFulOne $fetchFul, bool $useCache = true)
    {
        $table = $this->getModelTable($model, $fetchFul);
        $fetchFul->setTable($table);

        if (!$useCache) {
            $class = $this->getModel($model, $fetchFul);
            InjectExtension::callInjects($this->container, $class);
            $class->__init();
            return $class;
        }

        $cacheName = $this->getModelCacheName($model, $table, $fetchFul);
        if (isset($this->loaded[$cacheName])) {
            return $this->loaded[$cacheName];
        }

        $class = $this->cache->load($cacheName, function () use ($model, $fetchFul, $cacheName) {
            InjectExtension::callInjects($this->container, $fetchFul);
            return $this->getModel($model, $fetchFul);
        });

        $class::$dbTable = $table;
        $class->setCacheName($cacheName);
        InjectExtension::callInjects($this->container, $class);
        $class->__init();
        if ($this->rememberLoadedClasses) {
            $this->loaded[$cacheName] = $class;
        }
        return $class;
    }

    protected function getModel(string $model, FetchFulOne $fetchFul)
    {
        $class = new $model();

        $class::$dbTable = $class::$dbTable ?? $this->getModelTable($model, $fetchFul);
        $fetch = $fetchFul
            ->setTable($class::$dbTable)
            ->fetchOne()
        ;

        if (is_null($fetch)) {
            return $class;
        }

        $this->setValues($class, $fetch);
        return $class;
    }

    protected function setValues(Model $model, $fetch): void
    {
        foreach ($fetch as $column => $value) {
            $columnName = $this->caseConvertor->convert($column);
            $model->$columnName = $this
                ->typeMapper
                ->mapValue($value, $this->dataTypes->getDataType($model::$dbTable, $columnName)
                )
            ;
        }
    }

    protected function getModelTable(string $model, FetchFulOne $fetchFul): string
    {
        return $fetchFul->getTable()
            ?? $model::$dbTable
            ?? strtolower(str_replace('\\', '_', preg_replace(
                '#' . $this->mapping['model'] . '$#',
                '$1',
                $model
            )));
    }

    protected function getModelCacheName(string $model, string $table, FetchFulOne $fetchFul): string
    {
        $primaryColumn = $this->dataTypes[$table]['primary'];
        foreach ($primaryColumn as &$value) {
            $value .= '-' . $fetchFul->$value;
        }

        return str_replace('\\', '/', $model) . '/' . join('/', $primaryColumn);
    }

    public function save(Model $model): self
    {
        $set = [];
        $rowExists = true;
        $dataTypes = $this->dataTypes[$model::$dbTable];
        $updateWhere = [];
        foreach ($dataTypes['columns'] as $column => $value) {
            $dbColumn = $this->caseConvertor->reverse($column);
            if (in_array($column, $dataTypes['primary'])) {
                if (!isset($model->$column)) {
                    $rowExists = false;
                } else {
                    $updateWhere[$dbColumn] = $model->$column;
                }
            } else {
                $set[$dbColumn] = $this->typeMapper->saveValue($model->$column, $value);
            }
        }
        \Tracy\Debugger::barDump($rowExists);
        \Tracy\Debugger::barDump($updateWhere);

        if ($rowExists) {
            $this->explorer->table($model::$dbTable)->where($updateWhere)->update($set);
        } else {
            $inserted = $this->explorer->table($model::$dbTable)->insert($set);
            $this->setValues($model, $inserted);
        }

        $tmp = new $model;
        $tmp->setCacheName($model->getCacheName());

        foreach ($dataTypes['columns'] as $column => $value) {
            $tmp->$column = $model->$column;
        }
        \Tracy\Debugger::barDump($tmp);
        $this->cache->save($model->getCacheName(), $tmp);

        return $this;
    }
}
