<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Int4Range;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Range as RangeValueObject;

class Int4RangeArrayTypeTest extends ArrayTypeTestCase
{
    protected function getTypeName(): string
    {
        return 'int4range[]';
    }

    /**
     * @return array<string, array{array<Int4Range>}>
     */
    public static function provideValidTransformations(): array
    {
        return [
            'single int4range' => [[new Int4Range(1, 10, true, false)]],
            'multiple int4ranges' => [[
                new Int4Range(1, 5, true, false),
                new Int4Range(10, 20, true, false),
            ]],
            'int4range with negative values' => [[new Int4Range(-100, 100, true, false)]],
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
