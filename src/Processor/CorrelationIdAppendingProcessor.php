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
    private string $storeKey;

    public function __construct(
        CorrelationIdResolverInterface $resolver,
        ?string $storeKey = 'correlation_id'
    ) {
        $this->resolver = $resolver;
        $this->storeKey = $storeKey;
    }

    public function __invoke($record): array
    {
        if (Logger::API >= 3 && $record instanceof LogRecord) {
            $record = $record->toArray();
        }

        $record['extra'][$this->storeKey] = $this->resolver->resolve();

        return $record;
    }
}
