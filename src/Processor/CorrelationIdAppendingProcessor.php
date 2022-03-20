<?php

declare(strict_types=1);

namespace Profesia\Monolog\Processor;

use Monolog\Processor\ProcessorInterface;
use Profesia\Monolog\Extra\CorrelationIdResolver;

class CorrelationIdAppendingProcessor implements ProcessorInterface
{
    private CorrelationIdResolver $resolver;

    public function __construct(CorrelationIdResolver $resolver)
    {
        $this->resolver = $resolver;
    }

    public function __invoke(array $record): array
    {
        $record['extra']['correlation_id'] = $this->resolver->resolve();

        return $record;

    }
}
