<?php declare(strict_types=1);

namespace Leo\Model;

use Leo\Bridges\ModelCache;
use Nette\Database\Explorer;

trait ModelTrait
{
    protected static \Leo\ModelFactory $modelFactory;
    protected string $cacheName;

    public function injectModelFactory(\Leo\ModelFactory $modelFactory): static
    {
        self::$modelFactory = $modelFactory;
        return $this;
    }

    public function getModelFactory(): \Leo\ModelFactory
    {
        return self::$modelFactory;
    }

    public function getExplorer(): Explorer
    {
        return $this
            ->getModelFactory()
            ->getExplorer()
        ;
    }

    public function getCache(): ModelCache
    {
        return $this
            ->getModelFactory()
            ->getCache()
        ;
    }

    public function getCacheName(): string
    {
        return $this->cacheName;
    }

    public function setCacheName(string $cacheName): static
    {
        $this->cacheName = $cacheName;
        return $this;
    }

    public function __init(): self
    {
        return $this;
    }
}
