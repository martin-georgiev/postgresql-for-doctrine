<?php

namespace MartinGeorgiev\Tests\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Types\BigIntArray;
use PHPUnit_Framework_TestCase;

/**
 * @coversDefaultClass MartinGeorgiev\Tests\Doctrine\DBAL\Types\BigIntArray
 */
class BigIntArrayTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var BigIntArray
     */
    protected $dbalType;

    protected function setUp()
    {
        $this->dbalType = $this->getMockBuilder(BigIntArray::class)
            ->setMethods(null)
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * @covers ::getName
     */
    public function testTypeHasName()
    {
        $this->assertEquals('bigint[]', $this->dbalType->getName());
    }

    /**
     * @covers ::isValidArrayItemForDatabase
     */
    public function testIntegersAreValidArrayItems()
    {
        $arrayItems = ['-9223372036854775808', '9223372036854775807'];
        foreach ($arrayItems as $item) {
            $this->assertTrue($this->dbalType->isValidArrayItemForDatabase($item));
        }
    }

    /**
     * @covers ::isValidArrayItemForDatabase
     */
    public function testNonIntegersAreInvalidArrayItems()
    {
        $arrayItems = [true, null, -9223372036854775807.01, '-9223372036854775807.01', 'string', [], new \stdClass()];
        foreach ($arrayItems as $item) {
            $this->assertFalse($this->dbalType->isValidArrayItemForDatabase($item));
        }
    }

    /**
     * @covers ::transformArrayItemForPHP
     */
    public function testCanTransformIntegerArrayItemInPhpInteger()
    {
        $items = ['-9223372036854775808' => -9223372036854775808, '9223372036854775807' => 9223372036854775807];
        foreach ($items as $dbStoredValue => $expectedPhpValue) {
            $this->assertEquals($expectedPhpValue, $this->dbalType->transformArrayItemForPHP($dbStoredValue));
        }
    }
}
