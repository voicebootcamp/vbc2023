<?php

namespace Spatie\IcalendarGenerator\Enums;

use Spatie\Enum\Enum;

/**
 * @method static self public()
 * @method static self private()
 * @method static self confidential()
 */
class Classification extends Enum
{
    const MAP_VALUE = [
        'public' => 'PUBLIC',
        'private' => 'PRIVATE',
        'confidential' => 'CONFIDENTIAL',
    ];
}
