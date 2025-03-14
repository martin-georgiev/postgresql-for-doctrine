<?php

declare(strict_types=1);

namespace Tests\MartinGeorgiev\Utils;

use MartinGeorgiev\Utils\DataStructure;
use PHPUnit\Framework\TestCase;

class DataStructureTest extends TestCase
{
    /**
     * @test
     *
     * @dataProvider provideValidTransformations
     *
     * @param array<int, array<string, array|string>> $phpValue
     */
    public function can_transform_from_php_value(array $phpValue, string $postgresValue): void
    {
        self::assertEquals($postgresValue, DataStructure::transformPHPArrayToPostgresTextArray($phpValue));
    }

    /**
     * @test
     *
     * @dataProvider provideValidTransformations
     *
     * @param array<int, array<string, array|string>> $phpValue
     */
    public function can_transform_to_php_value(array $phpValue, string $postgresValue): void
    {
        self::assertEquals($phpValue, DataStructure::transformPostgresTextArrayToPHPArray($postgresValue));
    }

    /**
     * @see https://stackoverflow.com/a/27964420/3425372 Kudos to dmikam for the inspiration
     *
     * @return list<array{
     *     phpValue: array,
     *     postgresValue: string
     * }>
     */
    public static function provideValidTransformations(): array
    {
        return [
            [
                'phpValue' => [
                    0 => '1',
                    1 => '2',
                    2 => '3',
                    3 => '4',
                ],
                'postgresValue' => '{1,2,3,4}',
            ],
            [
                'phpValue' => [
                    0 => '1.23',
                    1 => '2.34',
                    2 => '3.45',
                    3 => '4.56',
                ],
                'postgresValue' => '{1.23,2.34,3.45,4.56}',
            ],
            [
                'phpValue' => [
                    0 => 'dfasdf',
                    1 => 'qw,,e{q"we',
                    2 => "'qrer'",
                    3 => 604,
                    4 => '"aaa","b""bb","ccc"',
                ],
                'postgresValue' => '{"dfasdf","qw,,e{q\"we","\'qrer\'",604,"\"aaa\",\"b\"\"bb\",\"ccc\""}',
            ],
            [
                'phpValue' => [
                    0 => '',
                    1 => '',
                ],
                'postgresValue' => '{"",""}',
            ],
            [
                'phpValue' => [],
                'postgresValue' => '{}',
            ],
        ];
    }

    /**
     * @test
     *
     * @dataProvider provideInvalidTransformations
     *
     * @param array<int, mixed> $phpValue
     */
    public function throws_invalid_argument_exception_when_tries_to_non_single_dimensioned_array_from_php_value(array $phpValue, string $postgresValue): void
    {
        $this->expectException(\InvalidArgumentException::class);
        DataStructure::transformPHPArrayToPostgresTextArray($phpValue);
    }

    /**
     * @test
     *
     * @dataProvider provideInvalidTransformations
     *
     * @param array<int, mixed> $phpValue
     */
    public function throws_invalid_argument_exception_when_tries_to_non_single_dimensioned_array_to_php_value(array $phpValue, string $postgresValue): void
    {
        $this->expectException(\InvalidArgumentException::class);
        DataStructure::transformPostgresTextArrayToPHPArray($postgresValue);
    }

    /**
     * @return list<array{
     *     phpValue: array,
     *     postgresValue: string
     * }>
     */
    public static function provideInvalidTransformations(): array
    {
        return [
            [
                'phpValue' => [
                    [
                        0 => '1-1',
                        1 => '1-2',
                        2 => '1-3',
                    ],
                    [
                        0 => '2-1',
                        1 => '2-2',
                        2 => '2-3',
                    ],
                ],
                'postgresValue' => '{{"1-1","1-2","1-3"},{"2-1","2-2","2-3"}}',
            ],
        ];
    }
}
