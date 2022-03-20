<?php

declare(strict_types=1);

namespace Profesia\Monolog\Extra;

class CorrelationIdResolver
{
    private string $correlationIdKey;
    private ?string $generatedId;

    public function __construct(string $correlationIdKey)
    {
        $this->correlationIdKey = $correlationIdKey;
        $this->generatedId      = null;
    }

    public function resolve(): string
    {
        if ($this->generatedId !== null) {
            return $this->generatedId;
        }

        $alreadyGeneratedCorrelationId = getenv($this->correlationIdKey);
        if ($alreadyGeneratedCorrelationId === '' || $alreadyGeneratedCorrelationId === false) {
            $this->generatedId = uniqid();
        } else {
            $this->generatedId = $alreadyGeneratedCorrelationId;
        }

        return $this->generatedId;
    }
}
