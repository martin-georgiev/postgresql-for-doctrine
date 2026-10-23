<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\NumericMultirange;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\NumericRange;
use PHPUnit\Framework\Attributes\Test;

final class NumMultirangeArrayTypeTest extends ArrayTypeTestCase
{
    protected function getTypeName(): string
    {
        return 'nummultirange[]';
    }

    /**
     * @return array<string, array{array<int, NumericMultirange|null>}>
     */
    public static function provideValidTransformations(): array
    {
        return [
            'single empty multirange' => [[new NumericMultirange([])]],
            'single multirange with one range' => [[new NumericMultirange([new NumericRange(1.5, 10.5)])]],
            'array of two multiranges' => [
                [
                    new NumericMultirange([new NumericRange(1, 5)]),
                    new NumericMultirange([new NumericRange(10.5, 20.5), new NumericRange(30.0, 40.0)]),
                ],
            ],
            'array with null item' => [[new NumericMultirange([new NumericRange(1.5, 10.5)]), null]],
        ];
    }

    #[Test]
    public function preserves_the_precision_of_a_bound_emitted_by_postgres(): void
    {
        $typeName = $this->getTypeName();
        $columnType = $this->getPostgresTypeName();

        $result = $this->fetchConvertedValueForPostgresLiteral($typeName, $columnType, '{"{[12345678901234567890.1,12345678901234567890.2)}"}');

        $this->assertIsArray($result);
        $this->assertCount(1, $result);
        $this->assertInstanceOf(NumericMultirange::class, $result[0]);
        $this->assertSame('{[12345678901234567890.1,12345678901234567890.2)}', (string) $result[0]);
    }
}
