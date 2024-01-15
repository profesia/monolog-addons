<?php

declare(strict_types=1);


namespace Profesia\Monolog\Test\Integration\Processor;


use Monolog\Level;
use Monolog\LogRecord;
use PHPUnit\Framework\TestCase;
use Profesia\Monolog\Processor\CorrelationIdAppendingProcessor;
use Profesia\Monolog\Test\Assets\TestCorrelationIdResolver;

class CorrelationIdAppendingProcessorTest extends TestCase
{
    public function provideDataForAlreadyGeneratedId(): array
    {
        //'message', 'context', 'level', 'channel', 'datetime', 'extra'
        $base = [
            'datetime' => new \DateTimeImmutable(),
            'context'  => [],
            'level'    => Level::Info,
            'channel'  => 'test',
            'message'  => 'Test message'
        ];
        return [
            [
                $base,
                [
                    'correlation_id' => TestCorrelationIdResolver::UUID,
                ],
            ],
            [
                new LogRecord(
                    $base['datetime'],
                    $base['channel'],
                    $base['level'],
                    $base['message'],
                    $base['context'],
                    []
                ),
                array_merge(
                    $base,
                    [
                        'correlation_id' => TestCorrelationIdResolver::UUID,
                    ]
                )
            ]
        ];
    }

    /**
     * @param $recordData
     * @param array $expected
     * @return void
     *
     * @dataProvider provideDataForAlreadyGeneratedId
     */
    public function testCanAppendAlreadyGeneratedIdToRecord($recordData, array $expected): void
    {
        $processor = new CorrelationIdAppendingProcessor(
            new TestCorrelationIdResolver()
        );

        $record = $processor->__invoke(
            $recordData
        );

        $this->assertEquals(
            $expected,
            $record instanceof LogRecord ? $record->toArray() : $record
        );
    }

    public function provideDataForOverrideCorrelationIdId(): array
    {
        $key = 'testing';
        //'message', 'context', 'level', 'channel', 'datetime', 'extra'
        $base = [
            'datetime' => new \DateTimeImmutable(),
            'context'  => [],
            'level'    => Level::Info,
            'channel'  => 'test',
            'message'  => 'Test message'
        ];

        return [
            [
                $base,
                [
                    $key => TestCorrelationIdResolver::UUID,
                ],
            ],
            [
                new LogRecord(
                    $base['datetime'],
                    $base['channel'],
                    $base['level'],
                    $base['message'],
                    $base['context'],
                    []
                ),
                [
                    $key => TestCorrelationIdResolver::UUID,
                ],
            ]
        ];
    }

    /**
     * @param $recordData
     * @param array $expected
     * @return void
     *
     * @dataProvider provideDataForOverrideCorrelationIdId
     */
    public function testCanOverrideCorrelationIdKey($recordData, array $expected): void
    {
        $key       = 'testing';
        $processor = new CorrelationIdAppendingProcessor(
            new TestCorrelationIdResolver(),
            $key
        );

        $record = $processor->__invoke(
            $recordData
        );

        if ($record instanceof LogRecord) {
            $recordPartToCompare = $record->toArray()['extra'];
        } else {
            $recordPartToCompare = $record['extra'];
        }

        $this->assertEquals(
            $expected,
            $recordPartToCompare
        );
    }
}
