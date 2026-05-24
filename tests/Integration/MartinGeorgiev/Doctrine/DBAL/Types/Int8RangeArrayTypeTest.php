<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Int8Range;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Range as RangeValueObject;

class Int8RangeArrayTypeTest extends ArrayTypeTestCase
{
    protected function getTypeName(): string
    {
        return 'int8range[]';
    }

    /**
     * @return array<string, array{array<Int8Range>}>
     */
    public static function provideValidTransformations(): array
    {
        return [
            'single int8range' => [[new Int8Range(1, 100, true, false)]],
            'multiple int8ranges' => [[
                new Int8Range(1, 50, true, false),
                new Int8Range(100, 200, true, false),
            ]],
            'int8range with large values' => [[new Int8Range(1000000000, 9999999999, true, false)]],
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
