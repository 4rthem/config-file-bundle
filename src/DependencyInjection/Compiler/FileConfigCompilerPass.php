<?php

declare(strict_types=1);

namespace Arthem\ConfigFileBundle\DependencyInjection\Compiler;

use Arthem\ConfigFileBundle\ConfigFileDumper;
use Arthem\ConfigFileBundle\DependencyInjection\Configuration;
use Symfony\Component\DependencyInjection\Compiler\AbstractRecursivePass;

class FileConfigCompilerPass extends AbstractRecursivePass
{
    private const PREFIX = '&file:';

    public function __construct(private readonly ConfigFileDumper $fileConfigManager)
    {
    }

    protected function processValue($value, bool $isRoot = false)
    {
        if (\is_string($value)) {
            if (str_starts_with($value, self::PREFIX)) {
                $rows = explode("\n", $value);
                $header = array_shift($rows);
                $content = $this->container->resolveEnvPlaceholders(
                    implode("\n", $rows),
                    true
                );

                $filename = substr($header, strlen(self::PREFIX));
                if (str_contains($filename, '.')) {
                    $filename = preg_replace(
                        '#\.([^.]+)$#',
                        sprintf('-%s.$1', Configuration::HASH_PLACEHOLDER),
                        $filename
                    );
                } else {
                    $filename .= '-'.Configuration::HASH_PLACEHOLDER;
                }

                $file = [
                    'filename' => $filename,
                    'content' => $content,
                ];

                return $this->fileConfigManager->dump($file);
            }
        }

        return parent::processValue($value, $isRoot);
    }
}
