<?php

declare(strict_types=1);

namespace Profesia\Monolog\Test\Integration\Extra;

use PHPUnit\Framework\TestCase;
use Profesia\Monolog\Extra\CorrelationIdResolver;
use Profesia\Monolog\Test\Integration\Assets\Uuid4Generator;

class CorrelationIdResolverTest extends TestCase
{
    public function testCanResolveIdFromEnvironmentParam(): void
    {
        $key      = 'key';
        $resolver = new CorrelationIdResolver(
            new Uuid4Generator(),
            $key
        );

        $resolvedId = $resolver->resolve();
        $this->assertEquals($key, $resolvedId);
    }

    public function testCanGenerateIdWhenEnvIdIsNotSet(): void
    {
        $key      = '';
        $resolver = new CorrelationIdResolver(
            new Uuid4Generator(),
            $key
        );

        $resolvedId = $resolver->resolve();
        $this->assertEquals(Uuid4Generator::UUID, $resolvedId);
        $this->assertEquals(Uuid4Generator::UUID, $resolver->resolve());
    }

    public function testCanStoreGeneratedIdIntoServer(): void
    {
        $resolver = new CorrelationIdResolver(
            new Uuid4Generator(),
            'test'
        );

        $resolver->store();

        $resolver = new CorrelationIdResolver(
            new Uuid4Generator(),
            'key'
        );

        $this->expectExceptionObject(new \RuntimeException('Not valid scenario'));
        $resolver->store();
    }
}
