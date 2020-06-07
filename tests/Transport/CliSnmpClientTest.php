<?php

declare(strict_types=1);

namespace SimPod\PhpSnmp\Tests\Transport;

use PHPUnit\Framework\TestCase;
use SimPod\PhpSnmp\Exception\CannotParseUnknownValueType;
use SimPod\PhpSnmp\Exception\EndOfMibReached;
use SimPod\PhpSnmp\Exception\InvalidVersionProvided;
use SimPod\PhpSnmp\Exception\NoSuchInstanceExists;
use SimPod\PhpSnmp\Transport\CliSnmpClient;
use function proc_open;
use function Safe\proc_get_status;
use function Safe\sprintf;
use function shell_exec;

final class CliSnmpClientTest extends TestCase
{
    private const SNMP_HOST = '127.0.0.1:15000';

    /** @var resource|null */
    private static $process;

    public static function setUpBeforeClass() : void
    {
        $command = 'snmpsimd.py --v2c-arch --data-dir %s --agent-udpv4-endpoint %s';
        $command = sprintf($command, __DIR__ . '/data', self::SNMP_HOST);

        $process = proc_open($command, [0 => ['file', '/dev/null', 'w'], 2 => ['file', '/dev/null', 'w']], $pipes);
        if ($process === false) {
            self::fail('failed to initiate SNMP agent');
        }

        self::$process = $process;
    }

    public static function tearDownAfterClass() : void
    {
        if (self::$process === null) {
            return;
        }

        shell_exec(sprintf('pkill -2 -P %d', proc_get_status(self::$process)['pid']));
        self::$process = null;
    }

    public function __destruct()
    {
        self::tearDownAfterClass();
    }

    public function testGet() : void
    {
        $result = $this->createCliSnmp()->get(['.1.3.6.1.2.1.25.2.3.1.2.1', '.1.3.6.1.2.1.25.2.3.1.2.4']);

        self::assertSame(
            [
                '.1.3.6.1.2.1.25.2.3.1.2.1' => '.1.3.6.1.2.1.25.2.1.2',
                '.1.3.6.1.2.1.25.2.3.1.2.4' => '.1.3.6.1.2.1.25.2.1.9',
            ],
            $result
        );
    }

    public function testGetNext() : void
    {
        $result = $this->createCliSnmp()->getNext(['.1.3.6.1.2.1.25.2.3.1.2', '.1.3.6.1.2.1.25.2.3.1.2.3']);

        self::assertSame(
            [
                '.1.3.6.1.2.1.25.2.3.1.2.1' => '.1.3.6.1.2.1.25.2.1.2',
                '.1.3.6.1.2.1.25.2.3.1.2.4' => '.1.3.6.1.2.1.25.2.1.9',
            ],
            $result
        );
    }

    public function testWalk() : void
    {
        $result = $this->createCliSnmp()->walk('.1.3.6.1.2.1.31.1.1.1.15');

        self::assertSame(
            [
                '.1.3.6.1.2.1.31.1.1.1.15.1000001' => 100000,
                '.1.3.6.1.2.1.31.1.1.1.15.1000003' => 60000,
                '.1.3.6.1.2.1.31.1.1.1.15.1000005' => 80000,
            ],
            $result
        );
    }

    public function testWalkWithOldSnmpVersion() : void
    {
        $result = $this->createCliSnmp('1')->walk('.1.3.6.1.2.1.31.1.1.1.15');

        self::assertSame(
            [
                '.1.3.6.1.2.1.31.1.1.1.15.1000001' => 100000,
                '.1.3.6.1.2.1.31.1.1.1.15.1000003' => 60000,
                '.1.3.6.1.2.1.31.1.1.1.15.1000005' => 80000,
            ],
            $result
        );
    }

    public function testWalkWholeTree() : void
    {
        $result = $this->createCliSnmp()->walk('.1.3');

        self::assertSame(
            [
                '.1.3.6.1.2.1.1.3.0' => 293718542,
                '.1.3.6.1.2.1.2.2.1.2.47' => 'Ethernet47',
                '.1.3.6.1.2.1.2.2.1.2.48' => 'Ethernet48',
                '.1.3.6.1.2.1.2.2.1.2.49001' => 'Ethernet49/1',
                '.1.3.6.1.2.1.2.2.1.2.50001' => 'Ethernet50/1',
                '.1.3.6.1.2.1.2.2.1.2.1000008' => 'Port-Channel8',
                '.1.3.6.1.2.1.2.2.1.2.1000009' => 'Port-Channel9',
                '.1.3.6.1.2.1.2.2.1.2.2002002' => 'Vlan2002',
                '.1.3.6.1.2.1.2.2.1.2.2002019' => 'Vlan2019',
                '.1.3.6.1.2.1.2.2.1.2.2002020' => 'Vlan2020',
                '.1.3.6.1.2.1.2.2.1.2.5000000' => 'Loopback0',
                '.1.3.6.1.2.1.2.2.1.14.8' => 0,
                '.1.3.6.1.2.1.2.2.1.14.9' => 226,
                '.1.3.6.1.2.1.2.2.1.14.10' => 256,
                '.1.3.6.1.2.1.2.2.1.14.11' => 296,
                '.1.3.6.1.2.1.4.20.1.1.10.100.192.2' => '10.100.192.2',
                '.1.3.6.1.2.1.4.20.1.1.10.110.27.254' => '10.110.27.254',
                '.1.3.6.1.2.1.4.20.1.1.66.208.216.74' => '66.208.216.74',
                '.1.3.6.1.2.1.4.22.1.2.2000955.185.152.67.97' => '91 E2 BA E3 5A 61',
                '.1.3.6.1.2.1.4.22.1.2.2000955.185.152.67.99' => '53 54 00 5F 41 D0',
                '.1.3.6.1.2.1.4.22.1.2.2000955.185.152.67.100' => '53 54 00 4C 5A 5D',
                '.1.3.6.1.2.1.4.22.1.2.2000955.185.152.67.102' => '53 54 00 A9 A8 3B',
                '.1.3.6.1.2.1.4.22.1.2.2000955.185.152.67.104' => '53 54 00 5A A0 CA',
                '.1.3.6.1.2.1.25.2.3.1.2.1' => '.1.3.6.1.2.1.25.2.1.2',
                '.1.3.6.1.2.1.25.2.3.1.2.2' => '.1.3.6.1.2.1.25.2.1.2',
                '.1.3.6.1.2.1.25.2.3.1.2.3' => '.1.3.6.1.2.1.25.2.1.2',
                '.1.3.6.1.2.1.25.2.3.1.2.4' => '.1.3.6.1.2.1.25.2.1.9',
                '.1.3.6.1.2.1.31.1.1.1.6.46' => '1884401752869190',
                '.1.3.6.1.2.1.31.1.1.1.6.47' => '1883620653799494',
                '.1.3.6.1.2.1.31.1.1.1.6.48' => '1884283891426650',
                '.1.3.6.1.2.1.31.1.1.1.6.49001' => '2494191363092125',
                '.1.3.6.1.2.1.31.1.1.1.6.50001' => '17658827020872235',
                '.1.3.6.1.2.1.31.1.1.1.15.1000001' => 100000,
                '.1.3.6.1.2.1.31.1.1.1.15.1000003' => 60000,
                '.1.3.6.1.2.1.31.1.1.1.15.1000005' => 80000,
                '.1.3.6.1.6.3.10.2.1.3.0' => 2937024,
            ],
            $result
        );
    }

    public function testInvalidVersion() : void
    {
        $this->expectException(InvalidVersionProvided::class);
        $this->expectExceptionMessage('Invalid or unsupported SNMP version "whatever"');

        $this->createCliSnmp('whatever')->walk('.1.15');
    }

    public function testWalkWithNoSuchInstanceError() : void
    {
        $this->expectException(NoSuchInstanceExists::class);
        $this->expectExceptionMessage('No Such Instance currently exists at this OID: .1.3.5');

        $this->createCliSnmp()->walk('.1.3.5');
    }

    public function testWalkWithSnmpVersion1AndNoSuchInstanceError() : void
    {
        $this->expectException(NoSuchInstanceExists::class);
        $this->expectExceptionMessage('No Such Instance currently exists at this OID: .1.3.5');

        $this->createCliSnmp('1')->walk('.1.3.5');
    }

    public function testWalkWithEndOfMibError() : void
    {
        $this->expectException(EndOfMibReached::class);
        $this->expectExceptionMessage(
            'No more variables left in this MIB View (It is past the end of the MIB tree), tried oid: .1.15'
        );

        $this->createCliSnmp()->walk('.1.15');
    }

    public function testWalkWithSnmpVersion1AndEndOfMibError() : void
    {
        $this->expectException(EndOfMibReached::class);
        $this->expectExceptionMessage('No more variables left in this MIB View (It is past the end of the MIB tree)');

        $this->createCliSnmp('1')->walk('.1.15');
    }

    public function testGetWithNoSuchInstanceError() : void
    {
        $this->expectException(NoSuchInstanceExists::class);
        $this->expectExceptionMessage('No Such Instance currently exists at this OID: .1.3.5');

        $this->createCliSnmp()->get(['.1.3.5']);
    }

    public function testGetWithSnmpVersion1AndNoSuchInstanceError() : void
    {
        $this->expectException(NoSuchInstanceExists::class);
        $this->expectExceptionMessage('No Such Instance currently exists at this OID: .1.3.5');

        $this->createCliSnmp('1')->get(['.1.3.5']);
    }

    public function testGetNextWithEndOfMibError() : void
    {
        $this->expectException(EndOfMibReached::class);
        $this->expectExceptionMessage(
            'No more variables left in this MIB View (It is past the end of the MIB tree), tried oid: .1.15'
        );

        $this->createCliSnmp()->getNext(['.1.15']);
    }

    public function testGetNextWithSnmpVersion1AndEndOfMibError() : void
    {
        $this->expectException(EndOfMibReached::class);
        $this->expectExceptionMessage(
            'No more variables left in this MIB View (It is past the end of the MIB tree), tried oid: .1.15'
        );

        $this->createCliSnmp('1')->getNext(['.1.15']);
    }

    public function testWalkWithUnknownTypeError() : void
    {
        $this->expectException(CannotParseUnknownValueType::class);
        $this->expectExceptionMessage('Encountered unknown value type "OPAQUE"');

        $this->createCliSnmp()->walk('.1.6.6.6.666');
    }

    private function createCliSnmp(string $version = '2c') : CliSnmpClient
    {
        return new CliSnmpClient(self::SNMP_HOST, 'public', 1, 3, $version);
    }
}
