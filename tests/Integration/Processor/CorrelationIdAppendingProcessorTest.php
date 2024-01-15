<?php

declare(strict_types=1);


namespace Profesia\Monolog\Test\Integration\Processor;


use Monolog\Level;
use Monolog\Logger;
use Monolog\LogRecord;
use PHPUnit\Framework\TestCase;
use Profesia\Monolog\Processor\CorrelationIdAppendingProcessor;
use Profesia\Monolog\Test\Assets\TestCorrelationIdResolver;
use Psr\Log\LogLevel;

class CorrelationIdAppendingProcessorTest extends TestCase
{
    public function provideDataForAlreadyGeneratedId(): array
    {
        if (class_exists('Monolog\Level')) {
            $logLevel = Level::Info;
        } else {
            $logLevel = LogLevel::INFO;
        }
        //'message', 'context', 'level', 'channel', 'datetime', 'extra'
        $base = [
            'datetime' => new \DateTimeImmutable(),
            'context'  => [],
            'level'    => $logLevel,
            'channel'  => 'test',
            'message'  => 'Test message'
        ];

        $returnArray = [
            'array already generated' => [
                $base,
                [
                    'correlation_id' => TestCorrelationIdResolver::UUID,
                ],
            ],
        ];

        if (class_exists('Monolog\LogRecord')) {
            $returnArray['log record generated'] = [
                new LogRecord(
                    $base['datetime'],
                    $base['channel'],
                    $base['level'],
                    $base['message'],
                    $base['context'],
                    []
                ),
                [
                    'correlation_id' => TestCorrelationIdResolver::UUID,
                ]
            ];
        }
        return $returnArray;
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

        if (Logger::API >= 3 && $record instanceof LogRecord) {
            $recordPartToCompare = $record->toArray()['extra'];
        } else {
            $recordPartToCompare = $record['extra'];
        }

        $this->assertEquals(
            $expected,
            $recordPartToCompare
        );
    }

    public function provideDataForOverrideCorrelationIdId(): array
    {
        if (class_exists('Monolog\Level')) {
            $logLevel = Level::Info;
        } else {
            $logLevel = LogLevel::INFO;
        }
        $key = 'testing';
        //'message', 'context', 'level', 'channel', 'datetime', 'extra'
        $base = [
            'datetime' => new \DateTimeImmutable(),
            'context'  => [],
            'level'    => $logLevel,
            'channel'  => 'test',
            'message'  => 'Test message'
        ];

        $returnArray = [
            'array override generation' => [
                $base,
                [
                    $key => TestCorrelationIdResolver::UUID,
                ],
            ]
        ];

        if (class_exists('Monolog\LogRecord')) {
            $returnArray['log record override generation'] = [
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
            ];
        }

        return $returnArray;
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

        if (Logger::API >= 3 && $record instanceof LogRecord) {
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
