<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\NumericRange;
use PHPUnit\Framework\Attributes\Test;

/**
 * @extends RangeArrayTypeTestCase<NumericRange>
 */
final class NumRangeArrayTypeTest extends RangeArrayTypeTestCase
{
    protected function getTypeName(): string
    {
        return 'numrange[]';
    }

    /**
     * @return class-string<NumericRange>
     */
    protected static function getRangeValueObjectClass(): string
    {
        return NumericRange::class;
    }

    /**
     * @return array<string, array{array<NumericRange|null>}>
     */
    public static function provideValidTransformations(): array
    {
        return [
            'single numrange' => [[new NumericRange(1.5, 10.7, true, false)]],
            'multiple numranges' => [[
                new NumericRange(1.5, 5.5, true, false),
                new NumericRange(10.0, 20.0, true, false),
            ]],
            'numrange with integer values' => [[new NumericRange(1, 100, true, false)]],
            'array with null item' => [[new NumericRange(1.5, 10.7, true, false), null]],
            'numrange bounded by NaN' => [[new NumericRange(1, NAN), new NumericRange(NAN, null)]],
        ];
    }

    #[Test]
    public function preserves_the_precision_of_a_bound_emitted_by_postgres(): void
    {
        $typeName = $this->getTypeName();
        $columnType = $this->getPostgresTypeName();

        $result = $this->fetchConvertedValueForPostgresLiteral($typeName, $columnType, '{"[12345678901234567890.1,12345678901234567890.2)"}');

        $this->assertIsArray($result);
        $this->assertCount(1, $result);
        $this->assertInstanceOf(NumericRange::class, $result[0]);
        $this->assertSame('[12345678901234567890.1,12345678901234567890.2)', (string) $result[0]);
    }
}
