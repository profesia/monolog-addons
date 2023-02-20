<?php

declare(strict_types=1);

namespace Profesia\Monolog\Test\Unit\Processor;

use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery\MockInterface;
use Profesia\CorrelationId\Resolver\CorrelationIdResolverInterface;
use Profesia\Monolog\Processor\CorrelationIdAppendingProcessor;

class CorrelationIdAppendingProcessorTest extends MockeryTestCase
{
    public function testCanAppendCorrelationIdToRecord(): void
    {
        $correlationId = 'test-id';
        /** @var MockInterface|CorrelationIdResolverInterface $resolver */
        $resolver = Mockery::mock(CorrelationIdResolverInterface::class);
        $resolver
            ->shouldReceive('resolve')
            ->once()
            ->andReturn(
                $correlationId
            );


        $processor = new CorrelationIdAppendingProcessor(
            $resolver
        );

        $record = $processor->__invoke(
            []
        );
        $this->assertEquals(
            [
                'extra' => [
                    'correlation_id' => $correlationId
                ]
            ],
            $record
        );
    }
}
