<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Range as RangeValueObject;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\TsRange;

class TsRangeArrayTypeTest extends ArrayTypeTestCase
{
    protected function getTypeName(): string
    {
        return 'tsrange[]';
    }

    /**
     * @return array<string, array{array<TsRange>}>
     */
    public static function provideValidTransformations(): array
    {
        return [
            'single tsrange' => [[new TsRange(
                new \DateTimeImmutable('2023-01-01 10:00:00'),
                new \DateTimeImmutable('2023-01-01 18:00:00'),
                false,
                false
            )]],
            'multiple tsranges' => [[
                new TsRange(
                    new \DateTimeImmutable('2023-01-01 08:00:00'),
                    new \DateTimeImmutable('2023-01-01 12:00:00'),
                    false,
                    false
                ),
                new TsRange(
                    new \DateTimeImmutable('2023-01-01 13:00:00'),
                    new \DateTimeImmutable('2023-01-01 17:00:00'),
                    false,
                    false
                ),
            ]],
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
