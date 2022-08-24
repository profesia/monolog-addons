<?php

declare(strict_types=1);

namespace Profesia\Monolog\Extra;

function getenv(?string $name, bool $local_only = false): array|string|false {
    return $name;
}

function putenv(string $assignment): bool
{
    if ($assignment !== 'test=test') {
        throw new \RuntimeException('Not valid scenario');
    }

    return true;
}

function php_sapi_name(): string|false {
    return 'apache';
}

function apache_setenv($variable, $value, $walk_to_top = false): bool
{
    if ($variable !== 'test') {
        throw new \RuntimeException('Not valid variable');
    }

    if ($value !== 'test') {
        throw new \RuntimeException('Not valid value');
    }

    return true;
}
