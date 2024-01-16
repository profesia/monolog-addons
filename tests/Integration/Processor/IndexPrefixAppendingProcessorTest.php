<?php

declare(strict_types=1);

namespace Profesia\Monolog\Test\Integration\Processor;

use Monolog\Level;
use Monolog\Logger;
use Monolog\LogRecord;
use PHPUnit\Framework\TestCase;
use Profesia\Monolog\Processor\IndexPrefixAppendingProcessor;
use Psr\Log\LogLevel;

class IndexPrefixAppendingProcessorTest extends TestCase
{
    public function provideGroupedChannels(): array
    {
        if (class_exists('Monolog\Level')) {
            $logLevel = Level::Info;
        } else {
            $logLevel = LogLevel::INFO;
        }
        $vendorName = 'testing';
        //'message', 'context', 'level', 'channel', 'datetime', 'extra'
        $base = [
            'datetime' => new \DateTimeImmutable(),
            'context'  => [],
            'level'    => $logLevel,
            'message'  => 'Test message'
        ];

        $returnArray = [
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
        ];

        if (class_exists('Monolog\LogRecord')) {
            $returnArray['log-record-single-channel'] = [
                $vendorName,
                [
                    'application' => ['app'],
                ],
                new LogRecord(
                    $base['datetime'],
                    'app',
                    $base['level'],
                    $base['message'],
                    $base['context'],
                    [
                        'index_prefix' => "{$vendorName}-application"
                    ]
                ),
                new LogRecord(
                    $base['datetime'],
                    'app',
                    $base['level'],
                    $base['message'],
                    $base['context'],
                    []
                ),
            ];

            $returnArray['log-record-more-channels'] = [
                $vendorName,
                [
                    'application' => ['app1', 'app2'],
                ],
                new LogRecord(
                    $base['datetime'],
                    'app2',
                    $base['level'],
                    $base['message'],
                    $base['context'],
                    [
                        'index_prefix' => "{$vendorName}-application"
                    ]
                ),
                new LogRecord(
                    $base['datetime'],
                    'app2',
                    $base['level'],
                    $base['message'],
                    $base['context'],
                    []
                ),
            ];

            $returnArray['log-record-more-groups' ] = [
                $vendorName,
                [
                    'application' => ['app1', 'app2'],
                    'external'    => ['communication', 'elastic']
                ],
                new LogRecord(
                    $base['datetime'],
                    'communication',
                    $base['level'],
                    $base['message'],
                    $base['context'],
                    [
                        'index_prefix' => "{$vendorName}-external"
                    ]
                ),
                new LogRecord(
                    $base['datetime'],
                    'communication',
                    $base['level'],
                    $base['message'],
                    $base['context'],
                    []
                ),
            ];

            $returnArray['log-record-non-existent-channel'] = [
                $vendorName,
                [
                    'application' => ['app1', 'app2'],
                    'external'    => ['communication', 'elastic']
                ],
                new LogRecord(
                    $base['datetime'],
                    'non-existent',
                    $base['level'],
                    $base['message'],
                    $base['context'],
                    [
                        'index_prefix' => "{$vendorName}-non-existent"
                    ]
                ),
                new LogRecord(
                    $base['datetime'],
                    'non-existent',
                    $base['level'],
                    $base['message'],
                    $base['context'],
                    []
                ),
            ];

            $returnArray['log-record-with-empty-channel'] = [
                $vendorName,
                [
                    'application' => ['app1', 'app2'],
                    'external'    => ['communication', 'elastic']
                ],
                new LogRecord(
                    $base['datetime'],
                    '',
                    $base['level'],
                    $base['message'],
                    $base['context'],
                    [
                        'index_prefix' => "{$vendorName}-" . IndexPrefixAppendingProcessor::CHANNEL_UNKNOWN,
                    ]
                ),
                new LogRecord(
                    $base['datetime'],
                    '',
                    $base['level'],
                    $base['message'],
                    $base['context'],
                    []
                ),
            ];
        }
        return $returnArray;
    }

    /**
     * @param string $vendorName
     * @param array $groupedChannels
     * @param $expected
     * @param $recordData
     *
     * @return void
     * @dataProvider provideGroupedChannels
     */
    public function testCanAppendChannelAsIndexPrefix(string $vendorName, array $groupedChannels, $expected, $recordData): void
    {
        $processor = new IndexPrefixAppendingProcessor(
            $vendorName,
            $groupedChannels
        );

        $record = $processor->__invoke(
            $recordData
        );

        if (Logger::API >= 3 && $record instanceof LogRecord) {
            $this->assertEquals(
                $expected->extra,
                $record->extra
            );
        } else {
            $this->assertEquals(
                $expected,
                $record['extra']
            );
        }
    }
}
