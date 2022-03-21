<?php

declare(strict_types=1);

namespace Profesia\Monolog\Test\Unit\Extra;

use Mockery\Adapter\Phpunit\MockeryTestCase;
use Profesia\Monolog\Extra\CorrelationIdResolver;

class CorrelationIdResolverTest extends MockeryTestCase
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
}

namespace Profesia\Monolog\Extra;

function getenv(?string $name, bool $local_only = false): array|string|false {
    return $name;
}

function uniqid(string $prefix = "", bool $more_entropy = false): string {
    return 'generated-id';
}
