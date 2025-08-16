<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\DBAL\Types;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

class BooleanArrayTypeTest extends ArrayTypeTestCase
{
    protected function getTypeName(): string
    {
        return 'bool[]';
    }

    protected function getPostgresTypeName(): string
    {
        return 'BOOL[]';
    }

    #[DataProvider('provideValidTransformations')]
    #[Test]
    public function can_handle_array_values(string $testName, array $arrayValue): void
    {
        parent::can_handle_array_values($testName, $arrayValue);
    }

    /**
     * @return array<string, array{string, array<int, bool>}>
     */
    public static function provideValidTransformations(): array
    {
        return [
            'simple boolean array' => ['simple boolean array', [true, false, true]],
            'boolean array with all true' => ['boolean array with all true', [true, true, true]],
            'boolean array with all false' => ['boolean array with all false', [false, false, false]],
            'boolean array mixed' => ['boolean array mixed', [true, false, true, false, true]],
        ];
    }
}
