<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\NumericRange;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Range as RangeValueObject;

class NumRangeArrayTypeTest extends ArrayTypeTestCase
{
    protected function getTypeName(): string
    {
        return 'numrange[]';
    }

    /**
     * @return array<string, array{array<NumericRange>}>
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
        ];
    }

    protected function assertTypeValueEquals(mixed $expected, mixed $actual, string $typeName): void
    {
        $this->assertIsArray($expected);
        $this->assertIsArray($actual);
        $this->assertCount(\count($expected), $actual, \sprintf('Array type %s round-trip count mismatch', $typeName));

        foreach ($expected as $index => $expectedItem) {
            $actualItem = $actual[$index];
            $this->assertInstanceOf(RangeValueObject::class, $expectedItem);
            $this->assertInstanceOf(RangeValueObject::class, $actualItem);
            $this->assertEquals(
                $expectedItem->__toString(),
                $actualItem->__toString(),
                \sprintf('Range string representation mismatch at index %d for type %s', $index, $typeName)
            );
        }
    }
}
