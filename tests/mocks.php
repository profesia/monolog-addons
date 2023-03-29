<?php

declare(strict_types=1);

namespace Profesia\Monolog\Extra;

/**
 * @param string|null $name
 * @param bool        $local_only
 *
 * @return array|string|bool
 */
function getenv(?string $name, bool $local_only = false): ?string {
    return $name;
}

function putenv(string $assignment): bool
{
    if ($assignment !== 'test=test') {
        throw new \RuntimeException('Not valid scenario');
    }

    return true;
}

/**
 * @return string|bool
 */
function php_sapi_name()  {
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
