<?php

namespace MartinGeorgiev\Tests\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Types\AbstractIntegerArray;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

abstract class AbstractIntegerArrayTest extends TestCase
{
    /**
     * @var AbstractIntegerArray|MockObject
     */
    protected $fixture;

    /**
     * @return array
     */
    public function invalidTransformations()
    {
        return [
            [true],
            [null],
            [-0.1],
            ['string'],
            [[]],
            [new \stdClass()],
        ];
    }

    /**
     * @test
     * @dataProvider invalidTransformations
     *
     * @param mixed $phpValue
     */
    public function can_detect_invalid_for_transformation_php_value($phpValue)
    {
        $this->assertFalse($this->fixture->isValidArrayItemForDatabase($phpValue));
    }

    /**
     * @return array
     */
    abstract public function validTransformations();

    /**
     * @test
     * @dataProvider validTransformations
     *
     * @param int $phpValue
     * @param string $postgresValue
     */
    public function can_transform_from_php_value($phpValue, $postgresValue)
    {
        $this->assertTrue($this->fixture->isValidArrayItemForDatabase($phpValue));
    }

    /**
     * @test
     * @dataProvider validTransformations
     *
     * @param int $phpValue
     * @param string $postgresValue
     */
    public function can_transform_to_php_value($phpValue, $postgresValue)
    {
        $this->assertEquals($phpValue, $this->fixture->transformArrayItemForPHP($postgresValue));
    }
}
