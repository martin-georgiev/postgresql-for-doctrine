<?php

namespace MartinGeorgiev\Tests\Utils;

use MartinGeorgiev\Utils\DataStructure;
use PHPUnit_Framework_TestCase;

/**
 * @covers MartinGeorgiev\Utils\DataStructure
 */
class DataStructureTest extends PHPUnit_Framework_TestCase
{
    /**
     * @see https://stackoverflow.com/a/27964420/3425372 Kudos to dmikam for the inspiration
     * @return array
     */
    private function getTestData()
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

    public function testCanTransformPhpArrayToPostgresTextArray()
    {
        foreach ($this->getTestData() as $testData) {
            $this->assertEquals($testData['postgresValue'], DataStructure::transformPHPArrayToPostgresTextArray($testData['phpValue']));
        }
    }

    public function testCanTransformPostgresTextArrayToPhpArray()
    {
        foreach ($this->getTestData() as $testData) {
            $this->assertEquals($testData['phpValue'], DataStructure::transformPostgresTextArrayToPHPArray($testData['postgresValue']));
        }
    }
}
