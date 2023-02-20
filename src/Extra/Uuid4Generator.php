<?php

declare(strict_types=1);

namespace Profesia\Monolog\Extra;

use Ramsey\Uuid\Uuid;

/**
 * @deprecated 
 */
final class Uuid4Generator implements CorrelationIdGeneratorInterface
{
    public function generate(): string
    {
        return Uuid::uuid4()->toString();
    }
}
