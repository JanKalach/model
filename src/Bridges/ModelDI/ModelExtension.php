<?php declare(strict_types=1);

namespace Leo\Bridges\ModelDI;

use Leo\Model\Convertors\NoCaseConvertor;
use Leo\ModelFactory;
use Nette;
use Nette\Schema\Expect;

class ModelExtension extends Nette\DI\CompilerExtension
{
    public function getConfigSchema(): Nette\Schema\Schema
    {
        return Expect::structure([
            'collection' => Expect::string()->default('#(.*(ModelModel|Entity))Collection$#'),
            'model' => Expect::string()->default('$1'),
            'caseConvertor' => Expect::string()->default(NoCaseConvertor::class),
        ]);
    }

    public function loadConfiguration()
    {
        $builder = $this->getContainerBuilder();
        $config = $this->getConfig();

        $builder
            ->addDefinition($this->prefix('caseConvertor'))
            ->setFactory($config->caseConvertor)
        ;
        $builder
            ->addDefinition($this->prefix('factory'))
            ->setFactory(ModelFactory::class, [
                '@database.default.explorer',
                '@model.caseConvertor',
                '@container',
                '@cache.storage',
            ])
            ->addSetup('mapClass', [$config->collection, $config->model])
        ;
    }
}
