<?php

declare(strict_types=1);

namespace Profesia\Monolog\Test\Integration\Extra;

use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery\MockInterface;
use Profesia\Monolog\Extra\CorrelationIdGeneratorInterface;
use Profesia\Monolog\Extra\CorrelationIdResolver;

class CorrelationIdResolverTest extends MockeryTestCase
{
    public function testCanResolveIdFromEnvironmentParam(): void
    {
        /** @var MockInterface|CorrelationIdGeneratorInterface $generator */
        $generator = Mockery::mock(CorrelationIdGeneratorInterface::class);
        $generator->shouldNotHaveReceived();

        $key      = 'key';
        $resolver = new CorrelationIdResolver(
            $generator,
            $key
        );

        $resolvedId = $resolver->resolve();
        $this->assertEquals($key, $resolvedId);
    }

    public function testCanGenerateIdWhenEnvIdIsNotSet(): void
    {
        $uuid = '7e8e94e2-bf74-4a52-a6de-5d33a8bd0836';
        /** @var MockInterface|CorrelationIdGeneratorInterface $generator */
        $generator = Mockery::mock(CorrelationIdGeneratorInterface::class);
        $generator
                ->shouldReceive('generate')
                ->once()
                ->andReturn($uuid);

        $key      = '';
        $resolver = new CorrelationIdResolver(
            $generator,
            $key
        );

        $resolvedId = $resolver->resolve();
        $this->assertEquals($uuid, $resolvedId);
    }

    public function testCanStoreGeneratedIdIntoServer(): void
    {
        /** @var MockInterface|CorrelationIdGeneratorInterface $generator */
        $generator = Mockery::mock(CorrelationIdGeneratorInterface::class);
        $generator->shouldNotHaveReceived();

        $resolver = new CorrelationIdResolver(
            $generator,
            'test'
        );

        $resolver->store();

        /** @var MockInterface|CorrelationIdGeneratorInterface $generator */
        $generator = Mockery::mock(CorrelationIdGeneratorInterface::class);
        $generator->shouldNotHaveReceived();

        $resolver = new CorrelationIdResolver(
            $generator,
            'key'
        );

        $this->expectExceptionObject(new \RuntimeException('Not valid scenario'));
        $resolver->store();
    }
}
