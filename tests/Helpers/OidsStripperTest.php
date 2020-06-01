<?php

declare(strict_types=1);

namespace SimPod\PhpSnmp\Tests\Helpers;

use PHPUnit\Framework\TestCase;
use SimPod\PhpSnmp\Helpers\OidStripper;
use SimPod\PhpSnmp\Transport\SnmpClient;

final class OidsStripperTest extends TestCase
{
    public function testStripLeafOidsParent() : void
    {
        $raw = [
            '.1.2.3.1' => 'a',
            '.1.2.3.2' => 'b',
            '.1.2.3.3' => 'c',
        ];

        $expected = [
            1 => 'a',
            2 => 'b',
            3 => 'c',
        ];

        self::assertSame($expected, OidStripper::stripLeafOidsParentOid($raw));
    }

    public function testWalk() : void
    {
        $raw = [
            '.1.2.3.1' => 'a',
            '.1.2.3.2' => 'b',
            '.1.2.3.3' => 'c',
        ];

        $expected = [
            '3.1' => 'a',
            '3.2' => 'b',
            '3.3' => 'c',
        ];

        $snmpClient = $this->createMock(SnmpClient::class);
        $snmpClient->expects(self::once())->method('walk')->with($oid = '.1.2')->willReturn($raw);

        self::assertSame($expected, OidStripper::walk($snmpClient, $oid));
    }
}
