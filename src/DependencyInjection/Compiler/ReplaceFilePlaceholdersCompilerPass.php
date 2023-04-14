<?php

declare(strict_types=1);

namespace Arthem\ConfigFileBundle\DependencyInjection\Compiler;

use Arthem\ConfigFileBundle\ConfigFileDumper;
use Arthem\ConfigFileBundle\DependencyInjection\ArthemConfigFileExtension;
use Symfony\Component\DependencyInjection\Compiler\AbstractRecursivePass;

class ReplaceFilePlaceholdersCompilerPass extends AbstractRecursivePass
{
    public function __construct(private readonly ConfigFileDumper $fileConfigManager)
    {
    }

    protected function processValue($value, bool $isRoot = false)
    {
        if (\is_string($value)) {
            if (str_contains($value, PrepareFilePlaceholderCompilerPass::PLACEHOLDER_PREFIX)) {
                $value = preg_replace_callback('#'.preg_quote(PrepareFilePlaceholderCompilerPass::PLACEHOLDER_PREFIX, '#').'([^%]+)%#', function (array $matches): string {
                    $key = $matches[1];
                    $file = $this->container->getParameter(ArthemConfigFileExtension::FILES_PARAM)[$key];

                    $file['content'] = $this->container->resolveEnvPlaceholders($file['content'], true);

                    return $this->fileConfigManager->dump($file);
                }, $value);
            }

            return $value;
        }

        return parent::processValue($value, $isRoot);
    }
}
