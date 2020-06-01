<?php

declare(strict_types=1);

namespace SimPod\PhpSnmp\Exception;

use RuntimeException;
use Throwable;
use function Safe\sprintf;

// phpcs:ignore SlevomatCodingStandard.Classes.SuperfluousExceptionNaming.SuperfluousSuffix
final class GeneralException extends RuntimeException implements SnmpException
{
    public static function new(string $error, ?Throwable $previous = null) : self
    {
        return new self(sprintf('Unexpected error: %s', $error), 0, $previous);
    }
}
