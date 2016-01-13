<?php

namespace MartinGeorgiev\Tests\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Types\IntegerArray;
use PHPUnit_Framework_TestCase;

/**
 * @coversDefaultClass MartinGeorgiev\Tests\Doctrine\DBAL\Types\IntegerArray
 */
class IntegerArrayTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var IntegerArray
     */
    protected $dbalType;

    protected function setUp()
    {
        $this->dbalType = $this->getMockBuilder(IntegerArray::class)
            ->setMethods(null)
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * @covers ::getName
     */
    public function testTypeHasName()
    {
        $this->assertEquals('integer[]', $this->dbalType->getName());
    }

    /**
     * @covers ::isValidArrayItemForDatabase
     */
    public function testIntegersAreValidArrayItems()
    {
        $arrayItems = [-2147483648, 2147483647, '-2147483648', '2147483647'];
        foreach ($arrayItems as $item) {
            $this->assertTrue($this->dbalType->isValidArrayItemForDatabase($item));
        }
    }

    /**
     * @covers ::isValidArrayItemForDatabase
     */
    public function testNonIntegersAreInvalidArrayItems()
    {
        $arrayItems = [true, null, -2147483647.01, '-2147483647.01', 'string', [], new \stdClass()];
        foreach ($arrayItems as $item) {
            $this->assertFalse($this->dbalType->isValidArrayItemForDatabase($item));
        }
    }

    /**
     * @covers ::transformArrayItemForPHP
     */
    public function testCanTransformIntegerArrayItemInPhpInteger()
    {
        $items = ['-2147483648' => -2147483648, '2147483647' => 2147483647];
        foreach ($items as $dbStoredValue => $expectedPhpValue) {
            $this->assertEquals($expectedPhpValue, $this->dbalType->transformArrayItemForPHP($dbStoredValue));
        }
    }
}
