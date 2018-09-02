<?php

declare(strict_types=1);

namespace MartinGeorgiev\Tests\Utils;

use MartinGeorgiev\Utils\DataStructure;
use PHPUnit\Framework\TestCase;

class DataStructureTest extends TestCase
{
    /**
     * @see https://stackoverflow.com/a/27964420/3425372 Kudos to dmikam for the inspiration
     */
    public function validTransformations(): array
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
                'postgresValue' => '{,}',
            ],
            [
                'phpValue' => [],
                'postgresValue' => '{}',
            ],
        ];
    }

    /**
     * @test
     * @dataProvider validTransformations
     */
    public function can_transform_from_php_value(array $phpValue, string $postgresValue): void
    {
        $this->assertEquals($postgresValue, DataStructure::transformPHPArrayToPostgresTextArray($phpValue));
    }

    /**
     * @test
     * @dataProvider validTransformations
     */
    public function can_transform_to_php_value(array $phpValue, string $postgresValue): void
    {
        $this->assertEquals($phpValue, DataStructure::transformPostgresTextArrayToPHPArray($postgresValue));
    }

    public function invalidTransformations(): array
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

    /**
     * @test
     * @dataProvider invalidTransformations
     */
    public function throws_InvalidArgumentException_when_tries_to_non_single_dimensioned_array_from_php_value(array $phpValue, string $postgresValue): void
    {
        $this->expectException(\InvalidArgumentException::class);
        DataStructure::transformPHPArrayToPostgresTextArray($phpValue);
    }

    /**
     * @test
     * @dataProvider invalidTransformations
     */
    public function throws_InvalidArgumentException_when_tries_to_non_single_dimensioned_array_to_php_value(array $phpValue, string $postgresValue): void
    {
        $this->expectException(\InvalidArgumentException::class);
        DataStructure::transformPostgresTextArrayToPHPArray($postgresValue);
    }
}
