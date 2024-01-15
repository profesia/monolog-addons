<?php

declare(strict_types=1);

namespace Profesia\Monolog\Test\Integration\Processor;

use Monolog\Level;
use Monolog\LogRecord;
use PHPUnit\Framework\TestCase;
use Profesia\Monolog\Processor\IndexPrefixAppendingProcessor;

class IndexPrefixAppendingProcessorTest extends TestCase
{
    public function provideGroupedChannels(): array
    {
        $vendorName = 'testing';
        //'message', 'context', 'level', 'channel', 'datetime', 'extra'
        $base = [
            'datetime' => new \DateTimeImmutable(),
            'context'  => [],
            'level'    => Level::Info,
            'message'  => 'Test message'
        ];

        return [
            'array-single-channel'      => [
                $vendorName,
                [
                    'application' => ['app'],
                ],
                [
                    'index_prefix' => "{$vendorName}-application",
                ],
                array_merge(
                    $base,
                    [
                        'channel' => 'app',
                    ],
                )
            ],
            'log-record-single-channel' => [
                $vendorName,
                [
                    'application' => ['app'],
                ],
                [
                    'index_prefix' => "{$vendorName}-application",
                ],
                new LogRecord(
                    $base['datetime'],
                    'app',
                    $base['level'],
                    $base['message'],
                    $base['context'],
                    []
                ),
            ],
            'array-more-channels' => [
                $vendorName,
                [
                    'application' => ['app1', 'app2'],
                ],
                [
                    'index_prefix' => "{$vendorName}-application",
                ],
                array_merge(
                    $base,
                    [
                        'channel' => 'app2',
                    ],
                )
            ],
            'log-record-more-channels' => [
                $vendorName,
                [
                    'application' => ['app1', 'app2'],
                ],
                [
                    'index_prefix' => "{$vendorName}-application",
                ],
                new LogRecord(
                    $base['datetime'],
                    'app2',
                    $base['level'],
                    $base['message'],
                    $base['context'],
                    []
                ),
            ],
            'array-more-groups' => [
                $vendorName,
                [
                    'application' => ['app1', 'app2'],
                    'external'    => ['communication', 'elastic']
                ],
                [
                    'index_prefix' => "{$vendorName}-external",
                ],
                array_merge(
                    $base,
                    [
                        'channel' => 'communication',
                    ],
                )
            ],
            'log-record--more-groups' => [
                $vendorName,
                [
                    'application' => ['app1', 'app2'],
                    'external'    => ['communication', 'elastic']
                ],
                [
                    'index_prefix' => "{$vendorName}-external",
                ],
                new LogRecord(
                    $base['datetime'],
                    'communication',
                    $base['level'],
                    $base['message'],
                    $base['context'],
                    []
                ),
            ],
            'array-non-existent-channel' => [
                $vendorName,
                [
                    'application' => ['app1', 'app2'],
                    'external'    => ['communication', 'elastic']
                ],
                [
                    'index_prefix' => "{$vendorName}-non-existent",

                ],
                array_merge(
                    $base,
                    [
                        'channel' => 'non-existent',
                    ],
                )
            ],
            'log-record-non-existent-channel' => [
                $vendorName,
                [
                    'application' => ['app1', 'app2'],
                    'external'    => ['communication', 'elastic']
                ],
                [
                    'index_prefix' => "{$vendorName}-non-existent",

                ],
                new LogRecord(
                    $base['datetime'],
                    'non-existent',
                    $base['level'],
                    $base['message'],
                    $base['context'],
                    []
                ),
            ],
            'array-without-channel' => [
                $vendorName,
                [
                    'application' => ['app1', 'app2'],
                    'external'    => ['communication', 'elastic']
                ],
                [
                    'index_prefix' => "{$vendorName}-" . IndexPrefixAppendingProcessor::CHANNEL_UNKNOWN,
                ],
                $base
            ],
            'log-record-with-empty-channel' => [
                $vendorName,
                [
                    'application' => ['app1', 'app2'],
                    'external'    => ['communication', 'elastic']
                ],
                [
                    'index_prefix' => "{$vendorName}-" . IndexPrefixAppendingProcessor::CHANNEL_UNKNOWN,
                ],
                new LogRecord(
                    $base['datetime'],
                    '',
                    $base['level'],
                    $base['message'],
                    $base['context'],
                    []
                ),
            ],
        ];
    }

    /**
     * @param string $vendorName
     * @param array $groupedChannels
     * @param array $expectedRecordPart
     * @param $recordData
     *
     * @return void
     * @dataProvider provideGroupedChannels
     */
    public function testCanAppendChannelAsIndexPrefix(string $vendorName, array $groupedChannels, array $expectedRecordPart, $recordData): void
    {
        $processor = new IndexPrefixAppendingProcessor(
            $vendorName,
            $groupedChannels
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
            $expectedRecordPart,
            $recordPartToCompare
        );
    }
}
