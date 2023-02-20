<?php

declare(strict_types=1);

namespace Profesia\Monolog\Processor;

use Monolog\Processor\ProcessorInterface;
use Profesia\CorrelationId\Resolver\CorrelationIdResolverInterface;

class CorrelationIdAppendingProcessor implements ProcessorInterface
{
    public function __construct(
        private CorrelationIdResolverInterface $resolver,
        private string $storeKey = 'correlation_id'
    )
    {
    }

    public function __invoke(array $record): array
    {
        $record['extra'][$this->storeKey] = $this->resolver->resolve();

        return $record;

    }
}
