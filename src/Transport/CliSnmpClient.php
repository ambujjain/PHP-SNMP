<?php

declare(strict_types=1);

namespace SimPod\PhpSnmp\Transport;

use SimPod\PhpSnmp\Exception\EndOfMibReached;
use SimPod\PhpSnmp\Exception\GeneralException;
use SimPod\PhpSnmp\Exception\InvalidVersionProvided;
use SimPod\PhpSnmp\Exception\NoSuchInstanceExists;
use SimPod\PhpSnmp\Exception\NoSuchObjectExists;
use SimPod\PhpSnmp\Transport\Cli\ProcessExecutor;
use SimPod\PhpSnmp\Transport\Cli\SymfonyProcessProcessExecutor;
use function array_merge;
use function count;
use function explode;
use function in_array;
use function Safe\preg_match;
use function Safe\preg_split;
use function strpos;

final class CliSnmpClient implements SnmpClient
{
    /** @var ProcessExecutor */
    private $processExecutor;

    /** @var string[] */
    private $processArgs;

    /** @var bool */
    private $useBulk;

    public function __construct(
        string $host = '127.0.0.1',
        string $community = 'public',
        int $timeout = 1,
        int $retries = 3,
        string $version = '2c',
        ?ProcessExecutor $processExecutor = null
    ) {
        if (! in_array($version, ['1', '2c'], true)) {
            throw InvalidVersionProvided::new($version);
        }

        $this->processExecutor = $processExecutor ?? new SymfonyProcessProcessExecutor(120);
        $this->processArgs     = [
            '-ObenU',
            '-t',
            (string) $timeout,
            '-r',
            (string) $retries,
            '-v',
            $version,
            '-c',
            $community,
            $host,
        ];
        $this->useBulk         = $version === '2c';
    }

    /** @inheritDoc */
    public function get(array $oids) : array
    {
        try {
            return $this->processOutput(
                $this->processExecutor->execute(array_merge(['snmpget'], $this->processArgs, $oids))
            );
        } catch (GeneralException $exception) {
            // check for SNMP v1
            if (preg_match('~\(noSuchName\).+Failed object: (.+?)$~ms', $exception->getMessage(), $matches) === 1) {
                throw NoSuchInstanceExists::fromOid($matches[1]);
            }

            throw $exception;
        }
    }

    /** @inheritDoc */
    public function getNext(array $oids) : array
    {
        try {
            return $this->processOutput(
                $this->processExecutor->execute(array_merge(['snmpgetnext'], $this->processArgs, $oids))
            );
        } catch (GeneralException $exception) {
            // check for SNMP v1
            if (preg_match('~\(noSuchName\).+Failed object: (.+?)$~ms', $exception->getMessage(), $matches) === 1) {
                throw EndOfMibReached::fromOid($matches[1]);
            }

            throw $exception;
        }
    }

    /** @inheritDoc */
    public function walk(string $oid) : array
    {
        $walker = $this->useBulk ? 'snmpbulkwalk' : 'snmpwalk';
        $output = $this->processExecutor->execute(array_merge([$walker], $this->processArgs, [$oid]));

        $result = $this->processOutput($output);
        if (count($result) === 0) {
            throw NoSuchInstanceExists::fromOid($oid);
        }

        return $result;
    }

    /** @return array<string, mixed> */
    private function processOutput(string $output) : array
    {
        $result = [];

        foreach (preg_split("~\r\n|\r|\n~", $output) as $line) {
            if ($line === '') {
                continue;
            }

            // check for SNMP v1
            if ($line === 'End of MIB') {
                throw EndOfMibReached::new();
            }

            [$oid, $value] = explode(' = ', $line, 2);

            if (strpos($value, 'No Such Object') === 0) {
                throw NoSuchObjectExists::fromOid($oid);
            }

            if (strpos($value, 'No Such Instance') === 0) {
                throw NoSuchInstanceExists::fromOid($oid);
            }

            if (strpos($value, 'No more variables left in this MIB View') === 0) {
                throw EndOfMibReached::fromOid($oid);
            }

            $result[$oid] = ValueParser::parse($value);
        }

        return $result;
    }
}
