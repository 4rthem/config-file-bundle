<?php

declare(strict_types=1);

namespace Arthem\ConfigFileBundle;

use Arthem\ConfigFileBundle\DependencyInjection\Compiler\FileConfigCompilerPass;
use Arthem\ConfigFileBundle\DependencyInjection\Compiler\PrepareFilePlaceholderCompilerPass;
use Arthem\ConfigFileBundle\DependencyInjection\Compiler\ReplaceFilePlaceholdersCompilerPass;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class ArthemConfigFileBundle extends Bundle
{
    public function getPath(): string
    {
        return \dirname(__DIR__);
    }

    public function build(ContainerBuilder $container)
    {
        $cacheDir = $container->getParameter('kernel.cache_dir');
        $configFileDumper = new ConfigFileDumper($cacheDir);

        $container->addCompilerPass(new FileConfigCompilerPass($configFileDumper), PassConfig::TYPE_REMOVE);
        $container->addCompilerPass(new PrepareFilePlaceholderCompilerPass(), PassConfig::TYPE_BEFORE_OPTIMIZATION);
        $container->addCompilerPass(new ReplaceFilePlaceholdersCompilerPass($configFileDumper), PassConfig::TYPE_REMOVE);
    }
}
