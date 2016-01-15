<?php

namespace MartinGeorgiev\Tests\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Types\JsonTransformer;
use PHPUnit_Framework_TestCase;

/**
 * @coversDefaultClass MartinGeorgiev\Tests\Doctrine\DBAL\Types\JsonTransformer
 */
class JsonTransformerTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var JsonTransformer 
     */
    private $transformer;

    protected function setUp()
    {
        $this->transformer = $this->getMockBuilder(JsonTransformer::class)
            ->setMethods(null)
            ->getMockForTrait();
    }

    /**
     * @return array
     */
    private function getTestData()
    {
        return [
            [
                'phpValue' => null,
                'postgresJson' => 'null',
            ],
            [
                'phpValue' => [],
                'postgresJson' => '[]',
            ],
            [
                'phpValue' => [681, 1185, 1878, 1989],
                'postgresJson' => '[681,1185,1878,1989]',
            ],
            [
                'phpValue' => [
                    'key1' => 'value1',
                    'key2' => false,
                    'key3' => '15',
                    'key4' => 15,
                    'key5' => [112, 242, 309, 310],
                ],
                'postgresJson' => '{"key1":"value1","key2":false,"key3":"15","key4":15,"key5":[112,242,309,310]}',
            ],
        ];
    }

    /**
     * @covers ::transformToPostgresJson
     */
    public function testCanTransformPhpValueToPostgresJson()
    {
        foreach ($this->getTestData() as $testData) {
            $this->assertEquals($testData['postgresJson'], $this->transformer->transformToPostgresJson($testData['phpValue']));
        }
    }

    /**
     * @covers ::transformFromPostgresJson
     */
    public function testCanTransformPostgresJsonToPhpValue()
    {
        foreach ($this->getTestData() as $testData) {
            $this->assertEquals($testData['phpValue'], $this->transformer->transformFromPostgresJson($testData['postgresJson']));
        }
    }
}
