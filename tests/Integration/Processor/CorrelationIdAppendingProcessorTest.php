<?php

declare(strict_types=1);


namespace Profesia\Monolog\Test\Integration\Processor;


use PHPUnit\Framework\TestCase;
use Profesia\Monolog\Processor\CorrelationIdAppendingProcessor;
use Profesia\Monolog\Test\Assets\TestCorrelationIdResolver;

class CorrelationIdAppendingProcessorTest extends TestCase
{
    public function testCanAppendAlreadyGeneratedIdToRecord(): void
    {
        $processor = new CorrelationIdAppendingProcessor(
            new TestCorrelationIdResolver()
        );

        $record      = $processor->__invoke(
            []
        );


        $this->assertEquals(
            [
                'extra' => [
                    'correlation_id' => TestCorrelationIdResolver::UUID,
                ],
            ],
            $record
        );
    }
}
