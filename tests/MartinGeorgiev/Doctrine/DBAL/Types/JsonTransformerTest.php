<?php

namespace MartinGeorgiev\Tests\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Types\JsonTransformer;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class JsonTransformerTest extends TestCase
{
    /**
     * @var JsonTransformer|MockObject
     */
    private $transformer;

    protected function setUp()
    {
        $this->transformer = $this->getMockBuilder(JsonTransformer::class)
            ->setMethods(null)
            ->disableOriginalConstructor()
            ->getMockForTrait();
    }

    /**
     * @return array
     */
    public function validTransformations()
    {
        return [
            [
                '$phpValue' => null,
                '$postgresValue' => 'null',
            ],
            [
                '$phpValue' => [],
                '$postgresValue' => '[]',
            ],
            [
                '$phpValue' => [681, 1185, 1878, 1989],
                '$postgresValue' => '[681,1185,1878,1989]',
            ],
            [
                '$phpValue' => [
                    'key1' => 'value1',
                    'key2' => false,
                    'key3' => '15',
                    'key4' => 15,
                    'key5' => [112, 242, 309, 310],
                ],
                '$postgresValue' => '{"key1":"value1","key2":false,"key3":"15","key4":15,"key5":[112,242,309,310]}',
            ],
        ];
    }

    /**
     * @test
     * @dataProvider validTransformations
     *
     * @param int $phpValue
     * @param string $postgresValue
     */
    public function can_transform_from_php_value($phpValue, $postgresValue)
    {
        $this->assertEquals($postgresValue, $this->transformer->transformToPostgresJson($phpValue));
    }

    /**
     * @test
     * @dataProvider validTransformations
     *
     * @param string|null $phpValue
     * @param string|null $postgresValue
     */
    public function can_transform_to_php_value($phpValue, $postgresValue)
    {
        $this->assertEquals($phpValue, $this->transformer->transformFromPostgresJson($postgresValue));
    }
}
