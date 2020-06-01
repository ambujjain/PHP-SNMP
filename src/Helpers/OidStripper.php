<?php

declare(strict_types=1);

namespace SimPod\PhpSnmp\Helpers;

use SimPod\PhpSnmp\Transport\SnmpClient;
use function array_key_first;
use function assert;
use function is_string;
use function Safe\substr;
use function strlen;
use function strrpos;

final class OidStripper
{
    /**
     * @param array<string, mixed> $raw
     *
     * @return array<int, mixed>
     */
    public static function stripLeafOidsParentOid(array $raw) : array
    {
        $firstKey = array_key_first($raw);
        assert(is_string($firstKey));

        $lastDotPos = strrpos($firstKey, '.');
        assert($lastDotPos !== false);

        $stripLength = $lastDotPos + 1;

        $result = [];
        foreach ($raw as $oid => $value) {
            $result[(int) substr($oid, $stripLength)] = $value;
        }

        return $result;
    }

    /** @return array<string, mixed> */
    public static function walk(SnmpClient $snmpClient, string $oid) : array
    {
        $stripLength = strlen($oid) + 1;

        $result = [];
        foreach ($snmpClient->walk($oid) as $childOid => $value) {
            $result[substr($childOid, $stripLength)] = $value;
        }

        return $result;
    }
}
