<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Type;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Interval as IntervalValueObject;

class IntervalArrayTypeTest extends ArrayTypeTestCase
{
    use IntervalAssertionTrait;

    protected function getTypeName(): string
    {
        return Type::INTERVAL_ARRAY;
    }

    protected function getPostgresTypeName(): string
    {
        return 'INTERVAL[]';
    }

    /**
     * @return array<string, array{array<int, IntervalValueObject>}>
     */
    public static function provideValidTransformations(): array
    {
        return [
            'single year interval' => [[
                IntervalValueObject::fromString('1 year'),
            ]],
            'time-only interval' => [[
                IntervalValueObject::fromString('04:05:06'),
            ]],
            'full interval' => [[
                IntervalValueObject::fromString('1 year 2 mons 3 days 04:05:06'),
            ]],
            'multiple intervals' => [[
                IntervalValueObject::fromString('1 year'),
                IntervalValueObject::fromString('2 mons'),
                IntervalValueObject::fromString('04:05:06'),
            ]],
            'negative interval' => [[
                IntervalValueObject::fromString('-04:05:06'),
            ]],
            'zero interval' => [[
                IntervalValueObject::fromString('00:00:00'),
            ]],
            'days only' => [[
                IntervalValueObject::fromString('30 days'),
            ]],
        ];
    }

    protected function assertTypeValueEquals(mixed $expected, mixed $actual, string $typeName): void
    {
        if (!\is_array($expected) || !\is_array($actual)) {
            parent::assertTypeValueEquals($expected, $actual, $typeName);

            return;
        }

        $this->assertCount(\count($expected), $actual, \sprintf('Interval array count mismatch for type %s', $typeName));

        foreach ($expected as $index => $expectedItem) {
            $this->assertInstanceOf(IntervalValueObject::class, $expectedItem);
            $this->assertIntervalEquals($expectedItem, $actual[$index], $typeName);
        }
    }
}
