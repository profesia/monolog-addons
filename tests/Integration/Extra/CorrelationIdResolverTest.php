<?php

declare(strict_types=1);

namespace Profesia\Monolog\Test\Integration\Extra;

use PHPUnit\Framework\TestCase;
use Profesia\Monolog\Extra\CorrelationIdResolver;

class CorrelationIdResolverTest extends TestCase
{
    public function testCanResolveIdFromEnvironmentParam(): void
    {
        $key      = 'key';
        $resolver = new CorrelationIdResolver(
            $key
        );

        $resolvedId = $resolver->resolve();
        $this->assertEquals($key, $resolvedId);
    }

    public function testCanGenerateIdWhenEnvIdIsNotSet(): void
    {
        $key      = '';
        $resolver = new CorrelationIdResolver(
            $key
        );

        $resolvedId = $resolver->resolve();
        $this->assertEquals('generated-id', $resolvedId);
    }

    public function testCanStoreGeneratedIdIntoServer(): void
    {
        $resolver = new CorrelationIdResolver(
            'test'
        );

        $resolver->store();

        $resolver = new CorrelationIdResolver(
            'key'
        );

        $this->expectExceptionObject(new \RuntimeException('Not valid scenario'));
        $resolver->store();
    }
}

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

function uniqid(string $prefix = "", bool $more_entropy = false): string {
    return 'generated-id';
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
