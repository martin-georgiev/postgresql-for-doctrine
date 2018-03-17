<?php

namespace MartinGeorgiev\Tests\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use MartinGeorgiev\Doctrine\DBAL\Types\JsonbArray;
use PHPUnit_Framework_TestCase;

/**
 * @coversDefaultClass MartinGeorgiev\Tests\Doctrine\DBAL\Types\JsonbArray
 */
class JsonbArrayTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var JsonbArray
     */
    protected $dbalType;

    /**
     * @var AbstractPlatform
     */
    protected $platform;

    protected function setUp()
    {
        $this->dbalType = $this->getMockBuilder(JsonbArray::class)
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
                'postgresJsonb' => null,
            ],
            [
                'phpValue' => [
                    [
                        'key1' => 'value1',
                        'key2' => false,
                        'key3' => '15',
                        'key4' => 15,
                        'key5' => [112, 242, 309, 310],
                    ],
                    [
                        'key1' => 'value2',
                        'key2' => true,
                        'key3' => '115',
                        'key4' => 115,
                        'key5' => [304, 404, 504, 604],
                    ],
                ],
                'postgresJsonb' => '{{"key1":"value1","key2":false,"key3":"15","key4":15,"key5":[112,242,309,310]},{"key1":"value2","key2":true,"key3":"115","key4":115,"key5":[304,404,504,604]}}',
            ],
        ];
    }

    /**
     * @covers ::getName
     */
    public function testTypeHasName()
    {
        $this->assertEquals('jsonb[]', $this->dbalType->getName());
    }

    /**
     * @covers ::convertToDatabaseValue
     * @covers ::transformArrayItemForPostgres
     */
    public function testCanTransformPhpValueToPostgresJson()
    {
        foreach ($this->getTestData() as $testData) {
            $this->assertEquals($testData['postgresJsonb'], $this->dbalType->convertToDatabaseValue($testData['phpValue'], $this->platform));
        }
    }

    /**
     * @covers ::convertToPHPValue
     * @covers ::transformPostgresArrayToPHPArray
     */
    public function testCanTransformPostgresJsonToPhpValue()
    {
        foreach ($this->getTestData() as $testData) {
            $this->assertEquals($testData['phpValue'], $this->dbalType->convertToPHPValue($testData['postgresJsonb'], $this->platform));
        }
    }
}
