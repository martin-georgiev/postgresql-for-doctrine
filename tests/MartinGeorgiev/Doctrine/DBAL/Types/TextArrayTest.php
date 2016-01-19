<?php

namespace MartinGeorgiev\Tests\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use MartinGeorgiev\Doctrine\DBAL\Types\TextArray;
use PHPUnit_Framework_TestCase;

/**
 * @coversDefaultClass MartinGeorgiev\Tests\Doctrine\DBAL\Types\TextArray
 */
class TextArrayTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var TextArray
     */
    protected $dbalType;
    
    /**
     * @var AbstractPlatform
     */
    protected $platform;

    protected function setUp()
    {
        $this->dbalType = $this->getMockBuilder(TextArray::class)
            ->setMethods(null)
            ->disableOriginalConstructor()
            ->getMock();

        $this->platform = $this->getMockBuilder(AbstractPlatform::class)
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
    }

    /**
     * @return array
     */
    private function getTestData()
    {
        return [
            [
                'phpValue' => null,
                'postgresValue' => null,
            ],
            [
                'phpValue' => [
                    'some text here',
                    'and some here',
                    'even here there is text',
                ],
                'postgresValue' => '{"some text here","and some here","even here there is text"}',
            ],
        ];
    }

    /**
     * @covers ::getName
     */
    public function testTypeHasName()
    {
        $this->assertEquals('text[]', $this->dbalType->getName());
    }

    /**
     * @covers ::convertToDatabaseValue
     * @covers ::transformToPostgresTextArray
     */
    public function testCanTransformPhpArrayToPostgresTextArray()
    {
        foreach ($this->getTestData() as $testData) {
            $this->assertEquals($testData['postgresValue'], $this->dbalType->convertToDatabaseValue($testData['phpValue'], $this->platform));
        }
    }

    /**
     * @covers ::convertToPHPValue
     * @covers ::transformFromPostgresTextArray
     */
    public function testCanTransformPostgresTextArrayToPhpArray()
    {
        foreach ($this->getTestData() as $testData) {
            $this->assertEquals($testData['phpValue'], $this->dbalType->convertToPHPValue($testData['postgresValue'], $this->platform));
        }
    }
}
