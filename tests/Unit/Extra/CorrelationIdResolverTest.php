<?php

declare(strict_types=1);

namespace Profesia\Monolog\Test\Unit\Extra;

use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery\MockInterface;
use Profesia\Monolog\Extra\CorrelationIdGeneratorInterface;
use Profesia\Monolog\Extra\CorrelationIdResolver;
use Profesia\Monolog\Test\Integration\Assets\Uuid4Generator;

class CorrelationIdResolverTest extends MockeryTestCase
{
    public function testCanResolve(): void
    {
        /** @var MockInterface|CorrelationIdGeneratorInterface $generator */
        $generator = Mockery::mock(CorrelationIdGeneratorInterface::class);

        $key = 'key';
        $resolver = new CorrelationIdResolver(
            $generator,
            $key
        );

        $correlationId = $resolver->resolve();
        $this->assertEquals($key, $correlationId);

        $uuid = Uuid4Generator::UUID;
        /** @var MockInterface|CorrelationIdGeneratorInterface $generator */
        $generator = Mockery::mock(CorrelationIdGeneratorInterface::class);
        $generator
            ->shouldReceive('generate')
            ->once()
            ->andReturn($uuid);

        $key = '';
        $resolver = new CorrelationIdResolver(
            $generator,
            $key
        );

        $correlationId = $resolver->resolve();
        $this->assertEquals($uuid, $correlationId);

        $sameCorrelationId = $resolver->resolve();
        $this->assertEquals($uuid, $sameCorrelationId);
    }

    public function testCanOverrideReturningOfAlreadyGenerated(): void
    {
        /** @var MockInterface|CorrelationIdGeneratorInterface $generator */
        $generator = Mockery::mock(CorrelationIdGeneratorInterface::class);
        $generator
            ->shouldReceive('generate')
            ->times(2)
            ->andReturn(
                'value1',
                'value2'
            );

        $resolver = new CorrelationIdResolver(
            $generator,
            '',
            true
        );

        $this->assertEquals('value1', $resolver->resolve());
        $this->assertEquals('value2', $resolver->resolve());
    }
}
