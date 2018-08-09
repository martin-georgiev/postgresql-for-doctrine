<?php

namespace MartinGeorgiev\Tests\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use MartinGeorgiev\Doctrine\DBAL\Types\AbstractTypeArray;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class AbstractTypeArrayTest extends TestCase
{
    /**
     * @var AbstractPlatform|MockObject
     */
    private $platform;

    /**
     * @var AbstractTypeArray|MockObject
     */
    private $fixture;

    protected function setUp()
    {
        $this->platform = $this->createMock(AbstractPlatform::class);

        $this->fixture = $this->getMockBuilder(AbstractTypeArray::class)
            ->setMethods(['isValidArrayItemForDatabase'])
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * @return array
     */
    public function validTransformations()
    {
        return [
            [
                'phpValue' => null,
                'postgresValue' => null,
            ],
            [
                'phpValue' => [],
                'postgresValue' => '{}',
            ],
            [
                'phpValue' => [681, 1185, 1878, 1989],
                'postgresValue' => '{681,1185,1878,1989}',
            ],
        ];
    }

    /**
     * @test
     * @dataProvider validTransformations
     *
     * @param array|null $phpValue
     * @param string|null $postgresValue
     */
    public function can_transform_from_php_value($phpValue, $postgresValue)
    {
        $this->fixture
            ->method('isValidArrayItemForDatabase')
            ->willReturn(true);

        $this->assertSame($postgresValue, $this->fixture->convertToDatabaseValue($phpValue, $this->platform));
    }

    /**
     * @test
     * @dataProvider validTransformations
     *
     * @param array|null $phpValue
     * @param string|null $postgresValue
     */
    public function can_transform_to_php_value($phpValue, $postgresValue)
    {
        $this->assertEquals($phpValue, $this->fixture->convertToPHPValue($postgresValue, $this->platform));
    }

    /**
     * @test
     */
    public function throws_InvalidArgumentException_when_php_value_is_not_array()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessageRegExp('/Given PHP value content type is not PHP array. Instead it is "\w+"./');

        $this->fixture->convertToDatabaseValue('invalid-php-value-type', $this->platform);
    }

    /**
     * @test
     */
    public function throws_ConversionException_when_invalid_array_item_value()
    {
        $this->expectException(ConversionException::class);
        $this->expectExceptionMessage('One or more of items given doesn\'t look like valid.');

        $this->fixture
            ->expects($this->once())
            ->method('isValidArrayItemForDatabase')
            ->willReturn(false);

        $this->fixture->convertToDatabaseValue([0], $this->platform);
    }

    /**
     * @test
     */
    public function throws_ConversionException_when_postgres_value_is_not_valid_php_array()
    {
        $this->expectException(ConversionException::class);
        $this->expectExceptionMessageRegExp('/Given PostgreSql value content type is not PHP string. Instead it is "\w+"./');

        $this->fixture->convertToPHPValue(681, $this->platform);
    }
}
