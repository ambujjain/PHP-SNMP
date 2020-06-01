<?php

declare(strict_types=1);

namespace SimPod\PhpSnmp\Transport;

use SimPod\PhpSnmp\Exception\CannotParseUnknownValueType;
use function explode;
use function Safe\substr;
use function strrpos;
use function trim;

final class ValueParser
{
    /** @return int|string */
    public static function parse(string $rawValue)
    {
        [$type, $value] = explode(': ', $rawValue, 2);

        $value = trim($value);

        switch ($type) {
            case 'Counter64':
            case 'Hex-STRING':
            case 'IpAddress':
            case 'OID':
                return $value;
            case 'STRING':
                return substr($value, 1, -1);
            case 'INTEGER':
            case 'Counter32':
            case 'Gauge32':
                return (int) $value;
            case 'Timeticks':
                return (int) substr($value, 1, (int) strrpos($value, ')') - 1);
        }

        throw CannotParseUnknownValueType::new($type);
    }
}
