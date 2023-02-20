<?php

declare(strict_types=1);

namespace Profesia\Monolog\Test\Assets;

use Profesia\CorrelationId\Resolver\CorrelationIdResolverInterface;

class TestCorrelationIdResolver implements CorrelationIdResolverInterface
{
    public const UUID = 'daeca952-f4b1-49fa-b453-0277e58ba189';

    public function __construct(
        private ?string $generatedValue = null
    )
    {}

    public function resolve(): string
    {
        if ($this->generatedValue === null) {
            return self::UUID;
        }

        return $this->generatedValue;
    }

    public function store(?string $value = null): void
    {
    }
}
