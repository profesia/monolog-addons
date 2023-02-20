<?php

declare(strict_types=1);

namespace Profesia\Monolog\Extra;

/**
 * @deprecated
 */
class CorrelationIdResolver
{
    private ?string $generatedId;

    public function __construct(
        private CorrelationIdGeneratorInterface $generator,
        private string $correlationIdKey,
    ) {
        $this->generatedId = null;
    }

    public function resolve(): string
    {
        if ($this->generatedId !== null) {
            return $this->generatedId;
        }

        $alreadyGeneratedCorrelationId = getenv($this->correlationIdKey);
        if ($alreadyGeneratedCorrelationId === '' || $alreadyGeneratedCorrelationId === false) {
            $alreadyGeneratedCorrelationId = $this->generator->generate();
        }

        $this->generatedId = $alreadyGeneratedCorrelationId;

        return $this->generatedId;
    }

    public function store(): void
    {
        $generatedId = $this->resolve();

        putenv($this->correlationIdKey . "={$generatedId}");

        $phpSapiName = php_sapi_name();

        if (
            is_string($phpSapiName)
            && str_starts_with($phpSapiName, 'apache') === true
        ) {
            apache_setenv($this->correlationIdKey, $generatedId);
        }
    }
}
