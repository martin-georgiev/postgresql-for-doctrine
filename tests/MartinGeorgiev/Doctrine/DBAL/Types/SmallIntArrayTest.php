<?php

namespace MartinGeorgiev\Tests\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Types\SmallIntArray;
use PHPUnit_Framework_TestCase;

/**
 * @coversDefaultClass MartinGeorgiev\Tests\Doctrine\DBAL\Types\SmallIntArray
 */
class SmallIntArrayTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var SmallIntArray
     */
    protected $dbalType;

    protected function setUp()
    {
        $this->dbalType = $this->getMockBuilder(SmallIntArray::class)
            ->setMethods(null)
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * @covers ::getName
     */
    public function testTypeHasName()
    {
        $this->assertEquals('smallint[]', $this->dbalType->getName());
    }

    /**
     * @covers ::isValidArrayItemForDatabase
     */
    public function testIntegersAreValidArrayItems()
    {
        $arrayItems = [-32768, 32767, '-32768', '32767'];
        foreach ($arrayItems as $item) {
            $this->assertTrue($this->dbalType->isValidArrayItemForDatabase($item));
        }
    }

    /**
     * @covers ::isValidArrayItemForDatabase
     */
    public function testNonIntegersAreInvalidArrayItems()
    {
        $arrayItems = [true, null, -32767.01, '-32767.01', 'string', [], new \stdClass()];
        foreach ($arrayItems as $item) {
            $this->assertFalse($this->dbalType->isValidArrayItemForDatabase($item));
        }
    }

    /**
     * @covers ::transformArrayItemForPHP
     */
    public function testCanTransformIntegerArrayItemInPhpInteger()
    {
        $items = ['-32768' => -32768, '32767' => 32767];
        foreach ($items as $dbStoredValue => $expectedPhpValue) {
            $this->assertEquals($expectedPhpValue, $this->dbalType->transformArrayItemForPHP($dbStoredValue));
        }
    }
}
