<?php

declare(strict_types=1);

namespace Arthem\ConfigFileBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\AbstractRecursivePass;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class PrepareFilePlaceholderCompilerPass extends AbstractRecursivePass
{
    final public const PARAM_PREFIX = '%arthem_config_file.file.';
    final public const PLACEHOLDER_PREFIX = '__arthem_config_file_file__';

    public function process(ContainerBuilder $container)
    {
        $this->container = $container;

        try {
            $bag = $this->container->getParameterBag();
            foreach ($bag->all() as $k => $value) {
                if (is_string($value) && str_contains($value, self::PARAM_PREFIX)) {
                    $bag->set($k, $this->processValue($value, true));
                }
            }

            $this->processValue($container->getDefinitions(), true);
        } finally {
            $this->container = null;
        }
    }

    protected function processValue($value, bool $isRoot = false)
    {
        if (\is_string($value)) {
            if (str_contains($value, self::PARAM_PREFIX)) {
                $value = preg_replace('#'.preg_quote(self::PARAM_PREFIX, '#').'([^%]+%)#', self::PLACEHOLDER_PREFIX.'$1', $value);
            }

            return $value;
        }

        return parent::processValue($value, $isRoot);
    }
}
