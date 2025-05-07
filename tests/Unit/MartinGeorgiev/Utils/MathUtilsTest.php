<?php

declare(strict_types=1);

namespace Tests\MartinGeorgiev\Utils;

use MartinGeorgiev\Utils\MathUtils;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class MathUtilsTest extends TestCase
{
    /**
     * @dataProvider providerInRange
     */
    public function test_in_range(
        null|float|int $value,
        null|float|int $start,
        null|float|int $end,
        bool $startInclusive,
        bool $endInclusive,
        bool $expected,
    ): void {
        self::assertEquals(
            $expected,
            MathUtils::inRange(
                (int) $value,
                $start,
                $end,
                $startInclusive,
                $endInclusive,
            )
        );

        self::assertEquals(
            $expected,
            MathUtils::inRange(
                (float) $value,
                $start,
                $end,
                $startInclusive,
                $endInclusive,
            )
        );

        self::assertFalse(
            MathUtils::inRange(
                null,
                $start,
                $end,
                $startInclusive,
                $endInclusive,
            )
        );
    }

    public static function providerInRange(): \Generator
    {
        foreach ([
            0 => false,
            1 => true,
            2 => true,
            3 => true,
            4 => false,
        ] as $value => $expected) {
            yield [
                'value' => $value,
                'start' => 1,
                'end' => 3,
                'startInclusive' => true,
                'endInclusive' => true,
                'expected' => $expected,
            ];
        }

        foreach ([
            0 => false,
            1 => false,
            2 => true,
            3 => true,
            4 => false,
        ] as $value => $expected) {
            yield [
                'value' => $value,
                'start' => 1,
                'end' => 3,
                'startInclusive' => false, // <-- this
                'endInclusive' => true,
                'expected' => $expected,
            ];
        }

        foreach ([
            0 => false,
            1 => true,
            2 => true,
            3 => false,
            4 => false,
        ] as $value => $expected) {
            yield [
                'value' => $value,
                'start' => 1,
                'end' => 3,
                'startInclusive' => true,
                'endInclusive' => false,  // <-- this
                'expected' => $expected,
            ];
        }

        foreach ([
            0 => false,
            1 => false,
            2 => true,
            3 => false,
            4 => false,
        ] as $value => $expected) {
            yield [
                'value' => $value,
                'start' => 1,
                'end' => 3,
                'startInclusive' => false,  // <-- this
                'endInclusive' => false,  // <-- this
                'expected' => $expected,
            ];
        }
    }

    /**
     * @dataProvider providerStringToNumber
     */
    public function test_string_to_number(?string $input, null|float|int $expected): void
    {
        self::assertEquals($expected, MathUtils::stringToNumber($input));
    }

    public static function providerStringToNumber(): \Generator
    {
        yield [null, null];

        yield ['foo', null];

        yield ['1+1', null];

        yield ['+1', 1];

        yield ['-1', -1];

        yield ['1', 1];

        yield ['1.0', 1.0];

        yield ['1.1', 1.1];

        yield ['2.2E+1', 22];
    }
}
