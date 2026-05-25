<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\DBAL\Types;

class TimetzArrayTypeTest extends ArrayTypeTestCase
{
    protected function getTypeName(): string
    {
        return 'timetz[]';
    }

    public static function provideValidTransformations(): array
    {
        return [
            'simple timetz array' => [['10:30:00+00', '14:45:00+02']],
            'array with negative offset' => [['08:00:00-05', '20:00:00+08']],
            'array with null item' => [[null, '10:30:00+00']],
        ];
    }
}
