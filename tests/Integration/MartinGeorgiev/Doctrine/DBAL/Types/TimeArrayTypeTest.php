<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\DBAL\Types;

final class TimeArrayTypeTest extends ArrayTypeTestCase
{
    protected function getTypeName(): string
    {
        return 'time[]';
    }

    public static function provideValidTransformations(): array
    {
        return [
            'simple time array' => [['10:30:00', '14:45:30']],
            'midnight and end of day' => [['00:00:00', '23:59:59']],
            'time with microseconds' => [['12:34:56.123456', '00:00:01.000001']],
            'array with null item' => [[null, '10:30:00']],
        ];
    }
}
