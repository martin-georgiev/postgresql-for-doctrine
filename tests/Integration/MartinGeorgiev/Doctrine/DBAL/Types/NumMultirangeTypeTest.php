<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\NumericMultirange as NumericMultirangeVO;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\NumericRange;
use PHPUnit\Framework\Attributes\Test;

final class NumMultirangeTypeTest extends MultirangeTypeTestCase
{
    protected function getTypeName(): string
    {
        return 'nummultirange';
    }

    /**
     * @return array<string, array{NumericMultirangeVO}>
     */
    public static function provideValidTransformations(): array
    {
        return [
            'empty multirange' => [new NumericMultirangeVO([])],
            'single decimal range' => [new NumericMultirangeVO([new NumericRange(1.5, 10.5)])],
            'two non-overlapping ranges' => [
                new NumericMultirangeVO([new NumericRange(1, 5), new NumericRange(10.5, 20.5)]),
            ],
            'range bounded by NaN' => [new NumericMultirangeVO([new NumericRange(1, NAN)])],
        ];
    }

    #[Test]
    public function preserves_the_precision_of_a_bound_emitted_by_postgres(): void
    {
        $typeName = $this->getTypeName();
        $columnType = $this->getPostgresTypeName();
        $postgresLiteral = '{[12345678901234567890.1,12345678901234567890.2)}';

        $result = $this->fetchConvertedValueForPostgresLiteral($typeName, $columnType, $postgresLiteral);

        $this->assertInstanceOf(NumericMultirangeVO::class, $result);
        $this->assertSame($postgresLiteral, (string) $result);
    }
}
