<?php

declare(strict_types=1);

namespace Arthem\ConfigFileBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class ArthemConfigFileExtension extends Extension
{
    final public const FILES_PARAM = 'arthem_config_file.config_files';

    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter(self::FILES_PARAM, $config['files']);

        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../config'));
        $loader->load('env_var_processor.yaml');
    }
}
