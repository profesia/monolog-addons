<?php

declare(strict_types=1);

namespace Profesia\Monolog\Test\Integration\Assets;

use Profesia\Monolog\Extra\CorrelationIdGeneratorInterface;

class StringGenerator implements CorrelationIdGeneratorInterface
{
    public function generate(): string
    {
        return 'test';
    }
}
