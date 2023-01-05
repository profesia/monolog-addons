<?php

declare(strict_types=1);

namespace Profesia\Monolog\Test\Integration\Processor;

use PHPUnit\Framework\TestCase;
use Profesia\Monolog\Processor\IndexPrefixAppendingProcessor;

class IndexPrefixAppendingProcessorTest extends TestCase
{
    public function provideGroupedChannels(): array
    {
        $vendorName = 'testing';

        return [
            [
                $vendorName,
                [
                    'application' => ['app'],
                ],
                [
                    'channel' => 'app',
                    'extra'   => [
                        'index_prefix' => "{$vendorName}-application",
                    ],
                ],
                [
                    'channel' => 'app',
                ],
            ],
            [
                $vendorName,
                [
                    'application' => ['app1', 'app2'],
                ],
                [
                    'channel' => 'app2',
                    'extra'   => [
                        'index_prefix' => "{$vendorName}-application",
                    ],
                ],
                [
                    'channel' => 'app2',
                ],
            ],
            [
                $vendorName,
                [
                    'application' => ['app1', 'app2'],
                    'external' => ['communication', 'elastic']
                ],
                [
                    'channel' => 'communication',
                    'extra'   => [
                        'index_prefix' => "{$vendorName}-external",
                    ],
                ],
                [
                    'channel' => 'communication',
                ],
            ],
            [
                $vendorName,
                [
                    'application' => ['app1', 'app2'],
                    'external' => ['communication', 'elastic']
                ],
                [
                    'channel' => 'non-existent',
                    'extra'   => [
                        'index_prefix' => "{$vendorName}-non-existent",
                    ],
                ],
                [
                    'channel' => 'non-existent',
                ],
            ],
            [
                $vendorName,
                [
                    'application' => ['app1', 'app2'],
                    'external' => ['communication', 'elastic']
                ],
                [
                    'extra'   => [
                        'index_prefix' => "{$vendorName}-" . IndexPrefixAppendingProcessor::CHANNEL_UNKNOWN,
                    ],
                ],
                [
                ],
            ],
        ];
    }

    /**
     * @param string $vendorName
     * @param array  $groupedChannels
     * @param array  $expectedRecordPart
     * @param array  $record
     *
     * @return void
     * @dataProvider provideGroupedChannels
     */
    public function testCanAppendChannelAsIndexPrefix(string $vendorName, array $groupedChannels, array $expectedRecordPart, array $record): void
    {
        $processor = new IndexPrefixAppendingProcessor(
            $vendorName,
            $groupedChannels
        );

        $this->assertEquals(
            $expectedRecordPart,
            $processor->__invoke(
                $record
            )
        );
    }
}
