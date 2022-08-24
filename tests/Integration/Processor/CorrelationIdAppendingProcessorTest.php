<?php

declare(strict_types=1);


namespace Profesia\Monolog\Test\Integration\Processor;


use PHPUnit\Framework\TestCase;
use Profesia\Monolog\Extra\CorrelationIdResolver;
use Profesia\Monolog\Extra\Uuid4Generator;
use Profesia\Monolog\Processor\CorrelationIdAppendingProcessor;

class CorrelationIdAppendingProcessorTest extends TestCase
{
    public function testCanAppendAlreadyGeneratedIdToRecord(): void
    {
        $generator = new Uuid4Generator();
        $resolver = new CorrelationIdResolver($generator, 'test-key');
        $processor = new CorrelationIdAppendingProcessor(
            $resolver
        );

        $generatedId = $resolver->resolve();
        $record      = $processor->__invoke(
            []
        );


        $this->assertEquals(
            [
                'extra' => [
                    'correlation_id' => $generatedId,
                ],
            ],
            $record
        );
    }

    public function testCanAppendNewGeneratedIdToRecord(): void
    {
        $generator = new Uuid4Generator();
        $resolver = new CorrelationIdResolver($generator, 'test-key');
        $processor = new CorrelationIdAppendingProcessor(
            $resolver
        );

        $record      = $processor->__invoke(
            []
        );


        $this->assertEquals(
            [
                'extra' => [
                    'correlation_id' => $resolver->resolve(),
                ],
            ],
            $record
        );
    }
}
