<?php

declare(strict_types=1);

namespace SimPod\PhpSnmp\Transport;

use SimPod\PhpSnmp\Exception\GeneralException;
use function count;

final class FallbackSnmpClient implements SnmpClient
{
    /** @var SnmpClient[] */
    private $snmpClients;

    public function __construct(SnmpClient ...$snmpClients)
    {
        if (count($snmpClients) === 0) {
            throw GeneralException::new('no SNMP clients provided');
        }

        $this->snmpClients = $snmpClients;
    }

    /** @inheritDoc */
    public function get(array $oids) : array
    {
        return $this->tryClients(
            static function (SnmpClient $client) use ($oids) : array {
                return $client->get($oids);
            }
        );
    }

    /** @inheritDoc */
    public function getNext(array $oids) : array
    {
        return $this->tryClients(
            static function (SnmpClient $client) use ($oids) : array {
                return $client->getNext($oids);
            }
        );
    }

    /** @inheritDoc */
    public function walk(string $oid) : array
    {
        return $this->tryClients(
            static function (SnmpClient $client) use ($oid) : array {
                return $client->walk($oid);
            }
        );
    }

    /** @return mixed */
    private function tryClients(callable $requestCallback)
    {
        foreach ($this->snmpClients as $snmpClient) {
            try {
                return $requestCallback($snmpClient);
            } catch (GeneralException $exception) {
                // try next client
            }
        }

        /** @phpstan-ignore-next-line exception will always be there */
        throw GeneralException::new('all SNMP clients failed, last error: ' . $exception->getMessage(), $exception);
    }
}
