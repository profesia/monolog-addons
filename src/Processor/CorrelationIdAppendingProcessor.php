<?php

declare(strict_types=1);

namespace Profesia\Monolog\Processor;

use Monolog\Logger;
use Monolog\LogRecord;
use Monolog\Processor\ProcessorInterface;
use Profesia\CorrelationId\Resolver\CorrelationIdResolverInterface;

class CorrelationIdAppendingProcessor implements ProcessorInterface
{
    private CorrelationIdResolverInterface $resolver;
    private string                         $storeKey;

    public function __construct(
        CorrelationIdResolverInterface $resolver,
        ?string $storeKey = 'correlation_id'
    )
    {
        $this->resolver = $resolver;
        $this->storeKey = $storeKey;
    }

    public function __invoke($record)
    {
        $isMonologVersion3 = (Logger::API >= 3 && $record instanceof LogRecord);
        if ($isMonologVersion3 === true) {
            $recordData = $record->toArray();
        } else {
            $recordData = $record;
        }

        $recordData['extra'][$this->storeKey] = $this->resolver->resolve();

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
