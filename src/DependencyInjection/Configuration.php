<?php

declare(strict_types=1);

namespace Arthem\ConfigFileBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    final public const HASH_PLACEHOLDER = '%hash%';

    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('arthem_config_file');

        $treeBuilder->getRootNode()
            ->children()
                ->arrayNode('files')
                    ->beforeNormalization()
                        ->always()
                        ->then(function (array $files) {
                            foreach ($files as $k => $v) {
                                $files[$k] = $this->normalizeFile($v, $k);
                            }

                            return $files;
                        })
                    ->end()
                    ->useAttributeAsKey('name')
                    ->arrayPrototype()
                        ->children()
                            ->scalarNode('content')->isRequired()->cannotBeEmpty()->end()
                            ->scalarNode('extension')->end()
                            ->scalarNode('filename')->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }

    private function normalizeFile(array|string $file, string $name): array
    {
        if (is_string($file)) {
            $file = [
                'content' => $file,
            ];
        }

        if (!isset($file['filename'])) {
            $file['filename'] = sprintf('%s-%s', $name, str_replace('%', '%%', self::HASH_PLACEHOLDER));
            if (!empty($file['extension'])) {
                $file['filename'] .= '.'.$file['extension'];
            }
        }

        return $file;
    }
}
