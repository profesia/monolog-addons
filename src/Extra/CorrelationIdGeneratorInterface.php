<?php

declare(strict_types=1);

namespace Profesia\Monolog\Extra;

/**
 * @deprecated
 */
interface CorrelationIdGeneratorInterface
{
    public function generate(): string;
}
