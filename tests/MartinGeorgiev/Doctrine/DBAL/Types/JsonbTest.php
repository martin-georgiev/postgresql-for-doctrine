<?php

namespace MartinGeorgiev\Tests\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use MartinGeorgiev\Doctrine\DBAL\Types\Jsonb;
use PHPUnit_Framework_TestCase;

/**
 * @coversDefaultClass MartinGeorgiev\Tests\Doctrine\DBAL\Types\Jsonb
 */
class JsonbTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Jsonb
     */
    protected $dbalType;
    /**
     * @var AbstractPlatform
     */
    protected $platfrom;

    protected function setUp()
    {
        $this->dbalType = $this->getMockBuilder(Jsonb::class)
            ->setMethods(null)
            ->disableOriginalConstructor()
            ->getMock();

        $this->platfrom = $this->getMockBuilder(AbstractPlatform::class)
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
                'phpValue' => [],
                'postgresJsonb' => '[]',
            ],
            [
                'phpValue' => [681, 1185, 1878, 1989],
                'postgresJsonb' => '[681,1185,1878,1989]',
            ],
            [
                'phpValue' => [
                    'key1' => 'value1',
                    'key2' => false,
                    'key3' => '15',
                    'key4' => 15,
                    'key5' => [112, 242, 309, 310],
                ],
                'postgresJsonb' => '{"key1":"value1","key2":false,"key3":"15","key4":15,"key5":[112,242,309,310]}',
            ],
        ];
    }

    /**
     * @covers ::getName
     */
    public function testTypeHasName()
    {
        $this->assertEquals('jsonb', $this->dbalType->getName());
    }

    /**
     * @covers ::convertToDatabaseValue
     */
    public function testCanTransformPhpValueInJsonb()
    {
        foreach ($this->getTestData() as $testData) {
            $this->assertEquals($testData['postgresJsonb'], $this->dbalType->convertToDatabaseValue($testData['phpValue'], $this->platfrom));
        }
    }

    /**
     * @covers ::convertToPHPValue
     */
    public function testCanTransformJsonbInPhpValue()
    {
        foreach ($this->getTestData() as $testData) {
            $this->assertEquals($testData['phpValue'], $this->dbalType->convertToPHPValue($testData['postgresJsonb'], $this->platfrom));
        }
    }
}
