<?php

declare(strict_types=1);

namespace Profesia\Monolog\Test\Unit\Extra;

use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery\MockInterface;
use Profesia\Monolog\Extra\CorrelationIdGeneratorInterface;
use Mockery;
use Profesia\Monolog\Extra\CorrelationIdResolver;

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

        $uuid = '7e8e94e2-bf74-4a52-a6de-5d33a8bd0836';
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
}
