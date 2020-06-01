<?php

declare(strict_types=1);

namespace SimPod\PhpSnmp\Transport;

interface SnmpClient
{
    /**
     * @param string[] $oids
     *
     * @return array<string, mixed>
     */
    public function get(array $oids) : array;

    /**
     * @param string[] $oids
     *
     * @return array<string, mixed>
     */
    public function getNext(array $oids) : array;

    /** @return array<string, mixed> */
    public function walk(string $oid) : array;
}
