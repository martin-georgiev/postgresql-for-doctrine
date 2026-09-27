<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\DBAL\Types;

use PHPUnit\Framework\Attributes\Test;

final class IntegerArrayTypeTest extends ArrayTypeTestCase
{
    protected function getTypeName(): string
    {
        return 'integer[]';
    }

    /**
     * @return array<string, array{array<int, int|null>}>
     */
    public static function provideValidTransformations(): array
    {
        return [
            'simple integer array' => [[1, 2, 3, 4, 5]],
            'integer array with negatives' => [[-1, 0, 1, -100, 100]],
            'integer array with max values' => [[2147483647, -2147483648, 0]],
            'array with a null element' => [[1, null, 3]],
        ];
    }

    #[Test]
    public function converts_values_emitted_by_postgres(): void
    {
        $typeName = $this->getTypeName();
        $columnType = $this->getPostgresTypeName();

        $result = $this->fetchConvertedValueForPostgresLiteral($typeName, $columnType, '[0:2]={5,1,2}');

        $this->assertSame([5, 1, 2], $result);
    }
}
