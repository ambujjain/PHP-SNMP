<?php

declare(strict_types=1);

namespace SimPod\PhpSnmp\Exception;

use RuntimeException;
use Throwable;
use function Safe\preg_match;
use function Safe\sprintf;

final class NoSuchObjectExists extends RuntimeException implements SnmpException
{
    private function __construct(string $message = '', ?Throwable $previous = null)
    {
        parent::__construct($message, 0, $previous);
    }

    public static function new() : self
    {
        return new self('No Such Object available on this agent at this OID');
    }

    public static function withOid(string $oid) : self
    {
        return new self(sprintf('No Such Object available on this agent at this OID: %s', $oid));
    }

    public static function fromThrowable(Throwable $throwable) : self
    {
        if (preg_match("~Error in packet at '(.+?)':~", $throwable->getMessage(), $matches) !== 1) {
            throw self::new();
        }

        return self::withOid($matches[1]);
    }
}
