<?php

declare(strict_types=1);

namespace Arthem\ConfigFileBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\EnvVarProcessorInterface;

class JsonEnvVarProcessor implements EnvVarProcessorInterface
{
    private const PREFIX = 'json_string';

    public function getEnv(string $prefix, string $name, \Closure $getEnv): string
    {
        return substr(json_encode($getEnv($name)), 1, -1);
    }

    public static function getProvidedTypes(): array
    {
        return [
            self::PREFIX => 'string',
        ];
    }
}
