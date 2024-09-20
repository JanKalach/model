<?php declare(strict_types=1);

namespace Leo\Bridges;

use Nette\Database\Explorer;

trait ModelFactoryTrait
{
    protected static \Leo\ModelFactory $modelFactory;

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
}
