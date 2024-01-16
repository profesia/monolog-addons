<?php

declare(strict_types=1);

namespace Profesia\Monolog\Processor;

use Monolog\Logger;
use Monolog\LogRecord;
use Monolog\Processor\ProcessorInterface;

class IndexPrefixAppendingProcessor implements ProcessorInterface
{
    public const CHANNEL_UNKNOWN = 'unknown-channel';

    private string $vendorName;
    /** @var string[] */
    private array $channelToGroupMap;

    /**
     * @param string     $vendorName
     * @param string[][] $groupedChannels
     */
    public function __construct(
        string $vendorName,
        array $groupedChannels
    ) {
        foreach ($groupedChannels as $group => $channels) {
            foreach ($channels as $channel) {
                $this->channelToGroupMap[$channel] = $group;
            }
        }

        $this->vendorName = $vendorName;
    }

    public function __invoke($record)
    {
        $isMonologVersion3 = (Logger::API >= 3 && $record instanceof LogRecord);
        if ($isMonologVersion3 === true) {
            $recordData = $record->toArray();
        } else {
            $recordData = $record;
        }

        $channel     = (isset($record['channel']) && $record['channel'] !== '') ? $record['channel'] : self::CHANNEL_UNKNOWN;
        $indexSuffix = $channel;

        if (array_key_exists($channel, $this->channelToGroupMap)) {
            $indexSuffix = $this->channelToGroupMap[$channel];
        }

        $recordData['extra']['index_prefix'] = "{$this->vendorName}-{$indexSuffix}";

        if ($isMonologVersion3 === true) {
            return new LogRecord(
                $record->datetime,
                $record->channel,
                $record->level,
                $record->message,
                $record->context,
                $recordData['extra'],
                $record->formatted
            );
        } else {
            return $recordData;
        }
    }
}
