<?php

namespace MartinGeorgiev\Tests\Utils;

use MartinGeorgiev\Utils\DataStructure;
use PHPUnit\Framework\TestCase;

class DataStructureTest extends TestCase
{
    /**
     * @see https://stackoverflow.com/a/27964420/3425372 Kudos to dmikam for the inspiration
     * @return array
     */
    public function validTransformations()
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
     *
     * @param array $phpValue
     * @param string $postgresValue
     */
    public function can_transform_from_php_value(array $phpValue, $postgresValue)
    {
        $this->assertEquals($postgresValue, DataStructure::transformPHPArrayToPostgresTextArray($phpValue));
    }

    /**
     * @test
     * @dataProvider validTransformations
     *
     * @param array $phpValue
     * @param string $postgresValue
     */
    public function can_transform_to_php_value(array $phpValue, $postgresValue)
    {
        $this->assertEquals($phpValue, DataStructure::transformPostgresTextArrayToPHPArray($postgresValue));
    }
}
