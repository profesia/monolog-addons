<?php

declare(strict_types=1);

namespace Profesia\Monolog\Processor;

class IndexPrefixAppendingProcessor
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

    public function __invoke(array $record): array
    {
        $channel     = (isset($record['channel']) && $record['channel'] !== '') ? $record['channel'] : self::CHANNEL_UNKNOWN;
        $indexSuffix = $channel;

        if (array_key_exists($channel, $this->channelToGroupMap)) {
            $indexSuffix = $this->channelToGroupMap[$channel];
        }

        $record['extra']['index_prefix'] = "{$this->vendorName}-{$indexSuffix}";

        return $record;
    }
}
