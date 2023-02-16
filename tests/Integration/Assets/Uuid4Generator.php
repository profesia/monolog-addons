<?php

declare(strict_types=1);

namespace Profesia\Monolog\Test\Integration\Assets;

use Profesia\Monolog\Extra\CorrelationIdGeneratorInterface;

class Uuid4Generator implements CorrelationIdGeneratorInterface
{
    public const UUID = '7e8e94e2-bf74-4a52-a6de-5d33a8bd0836';

    public function generate(): string
    {
        return self::UUID;
    }
}
