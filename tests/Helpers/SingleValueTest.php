<?php

declare(strict_types=1);

namespace SimPod\PhpSnmp\Tests\Helpers;

use PHPUnit\Framework\TestCase;
use SimPod\PhpSnmp\Helpers\SingleValue;

final class SingleValueTest extends TestCase
{
    public function testGetFirst() : void
    {
        $raw = ['.1.2.3.1' => 'a'];

        $expected = 'a';

        self::assertSame($expected, SingleValue::get($raw));
    }
}
