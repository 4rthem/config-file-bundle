<?php

declare(strict_types=1);

namespace Arthem\ConfigFileBundle;

use Arthem\ConfigFileBundle\DependencyInjection\Configuration;

class ConfigFileDumper
{
    public function __construct(
        private readonly string $cacheDir,
    ) {
    }

    /**
     * @return string The generated file path
     */
    public function dump(array $file): string
    {
        if (empty($file['content'])) {
            throw new \InvalidArgumentException('Missing or empty file content');
        }

        $hash = md5($file['content']);
        $target = str_replace(Configuration::HASH_PLACEHOLDER, $hash, $file['filename']);

        $cacheDir = $this->cacheDir.'/config-files';
        $path = $cacheDir.DIRECTORY_SEPARATOR.$target;

        if (!file_exists($path)) {
            if (!is_dir($cacheDir)) {
                if (false === @mkdir($cacheDir, 0777, true) && !is_dir($cacheDir)) {
                    throw new \RuntimeException(sprintf('Unable to create the ArthemConfigFile cache directory "%s".', $cacheDir));
                }
            } elseif (!is_writable($cacheDir)) {
                throw new \RuntimeException(sprintf('The ArthemConfigFile directory "%s" is not writeable for the current system user.', $cacheDir));
            }

            file_put_contents($path, $file['content']);
        }

        return $path;
    }
}
