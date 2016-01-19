<?php

namespace MartinGeorgiev\Tests\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use MartinGeorgiev\Doctrine\DBAL\Types\AbstractTypeArray;
use PHPUnit_Framework_TestCase;

/**
 * @coversDefaultClass MartinGeorgiev\Tests\Doctrine\DBAL\Types\AbstractTypeArray
 */
class AbstractTypeArrayTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var AbstractTypeArray
     */
    protected $dbalType;
    /**
     * @var AbstractPlatform
     */
    protected $platform;

    protected function setUp()
    {
        $this->dbalType = $this->getMockBuilder(AbstractTypeArray::class)
            ->setMethods(['isValidArrayItemForDatabase'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

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
                'phpArray' => null,
                'postgresArray' => null,
            ],
            [
                'phpArray' => [],
                'postgresArray' => '{}',
            ],
            [
                'phpArray' => [681, 1185, 1878, 1989],
                'postgresArray' => '{681,1185,1878,1989}',
            ],
        ];
    }

    /**
     * @covers ::convertToDatabaseValue
     * @covers ::isValidArrayItemForDatabase
     */
    public function testCanConvertDatabaseValueToPhpValue()
    {
        $this->dbalType->expects($this->any())
            ->method('isValidArrayItemForDatabase')
            ->will($this->returnValue(true));

        foreach ($this->getTestData() as $testData) {
            $this->assertEquals($testData['postgresArray'], $this->dbalType->convertToDatabaseValue($testData['phpArray'], $this->platform));
        }
    }

    /**
     * @expectedException Doctrine\DBAL\Types\ConversionException
     * @expectedExceptionMessageRegExp /Given PHP value content type is not PHP array. Instead it is "\w+"./
     */
    public function testThrowsAnExceptionWhenPhpValueIsNotArray()
    {
        $this->dbalType->convertToDatabaseValue('invalid-php-value-type', $this->platform);
    }

    /**
     * @expectedException Doctrine\DBAL\Types\ConversionException
     * @expectedExceptionMessage One or more of items given doesn't look like valid.
     */
    public function testThrowsAnExceptionOnInvalidArrayItemValue()
    {
        $this->dbalType->expects($this->any())
            ->method('isValidArrayItemForDatabase')
            ->will($this->returnValue(false));

        $this->dbalType->convertToDatabaseValue([0], $this->platform);
    }

    /**
     * @covers ::convertToPHPValue
     * @covers ::transformPostgresArrayToPHPArray
     * @covers ::transformArrayItemForPHP
     */
    public function testCanConvertPhpValueToDatabaseValue()
    {
        foreach ($this->getTestData() as $testData) {
            $this->assertEquals($testData['phpArray'], $this->dbalType->convertToPHPValue($testData['postgresArray'], $this->platform));
        }
    }

    /**
     * @expectedException Doctrine\DBAL\Types\ConversionException
     * @expectedExceptionMessageRegExp /Given PostgreSql value content type is not PHP string. Instead it is "\w+"./
     */
    public function testThrowsAnExceptionWhenPostgresValueIsNotPhpArray()
    {
        $this->dbalType->convertToPHPValue([], $this->platform);
    }
}
