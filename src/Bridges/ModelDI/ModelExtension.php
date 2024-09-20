<?php declare(strict_types=1);

namespace Leo\Bridges\ModelDI;

use Leo;
use Nette;
use Nette\Schema\Expect;

class ModelExtension extends Nette\DI\CompilerExtension
{
    public function getConfigSchema(): Nette\Schema\Schema
    {
        return Expect::structure([
            'collection' => Expect::string()->default('#(.*(ModelModel|Entity))Collection$#'),
            'model' => Expect::string()->default('$1'),
            'caseConvertor' => Expect::string()->default(Leo\Model\CaseConvertor\NoChange::class),
            'typeMapper' => Expect::string()->default(Leo\Bridges\Database\MySqlTypeMapper::class),
            'rememberLoadedClasses' => Expect::bool(true),
            'mapping' => Expect::structure([
                'model' => Expect::string()->default('App\\Model\\*\\*Model'),
                'collection' => Expect::string()->default('App\\Model\\*\\*Collection'),
                'entity' => Expect::string()->default('App\\Model\\*\\*Entity'),
                'entityCollection' => Expect::string()->default('App\\Model\\*\\*EntityCollection'),
            ])
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
            ->addDefinition($this->prefix('typeMapper'))
            ->setFactory($config->typeMapper)
        ;

        $builder
            ->addDefinition($this->prefix('cache'))
            ->setFactory(Leo\Bridges\ModelCache::class, ['@cache.storage', 'models'])
        ;

        $builder
            ->addDefinition($this->prefix('factory'))
            ->setFactory(Leo\ModelFactory::class, [
                '@database.default.explorer',
                '@model.caseConvertor',
                '@container',
                '@model.cache',
                '@model.typeMapper',
            ])
            ->addSetup('setMapping', [$config->mapping])
            ->addSetup('setRememberLoadedClasses', [$config->rememberLoadedClasses])
        ;
    }
}
