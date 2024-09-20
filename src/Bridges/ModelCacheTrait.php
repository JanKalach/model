<?php declare(strict_types=1);

namespace Leo\Bridges;

trait ModelCacheTrait
{
    protected string $cacheName;
    protected static ModelCache $cache;

    public function injectCache(ModelCache $cache): static
    {
        self::$cache = $cache;
        return $this;
    }

    public function getCache(): ModelCache
    {
        return self::$cache;
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
}
