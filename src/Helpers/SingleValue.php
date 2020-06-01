<?php

declare(strict_types=1);

namespace SimPod\PhpSnmp\Helpers;

use function array_shift;

final class SingleValue
{
    /**
     * @param array<string, mixed> $raw
     *
     * @return mixed
     */
    public static function get(array $raw)
    {
        return array_shift($raw);
    }
}
