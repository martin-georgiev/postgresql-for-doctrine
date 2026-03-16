<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidPolygonForDatabaseException;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidPolygonForPHPException;
use MartinGeorgiev\Doctrine\DBAL\Types\Polygon;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Polygon as PolygonValueObject;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class PolygonTest extends TestCase
{
    /**
     * @var AbstractPlatform&MockObject
     */
    private MockObject $platform;

    private Polygon $fixture;

    protected function setUp(): void
    {
        $this->platform = $this->createMock(AbstractPlatform::class);
        $this->fixture = new Polygon();
    }

    #[Test]
    public function has_name(): void
    {
        $this->assertSame('polygon', $this->fixture->getName());
    }

    #[Test]
    public function converts_null_to_database(): void
    {
        $this->assertNull($this->fixture->convertToDatabaseValue(null, $this->platform));
    }

    #[Test]
    public function converts_null_from_database(): void
    {
        $this->assertNull($this->fixture->convertToPHPValue(null, $this->platform));
    }

    #[DataProvider('provideValidTransformations')]
    #[Test]
    public function can_transform_from_php_value(PolygonValueObject $polygonValueObject, string $postgresValue): void
    {
        $this->assertSame($postgresValue, $this->fixture->convertToDatabaseValue($polygonValueObject, $this->platform));
    }

    #[DataProvider('provideValidTransformations')]
    #[Test]
    public function can_transform_to_php_value(PolygonValueObject $polygonValueObject, string $postgresValue): void
    {
        $result = $this->fixture->convertToPHPValue($postgresValue, $this->platform);
        $this->assertInstanceOf(PolygonValueObject::class, $result);
        $this->assertSame($postgresValue, (string) $result);
    }

    /**
     * @return array<string, array{polygonValueObject: PolygonValueObject, postgresValue: string}>
     */
    public static function provideValidTransformations(): array
    {
        return [
            'triangle' => [
                'polygonValueObject' => new PolygonValueObject('((0,0),(1,0),(0,1))'),
                'postgresValue' => '((0,0),(1,0),(0,1))',
            ],
            'square' => [
                'polygonValueObject' => new PolygonValueObject('((0,0),(1,0),(1,1),(0,1))'),
                'postgresValue' => '((0,0),(1,0),(1,1),(0,1))',
            ],
            'polygon with floats' => [
                'polygonValueObject' => new PolygonValueObject('((1.5,2.5),(3.5,4.5),(5.5,6.5))'),
                'postgresValue' => '((1.5,2.5),(3.5,4.5),(5.5,6.5))',
            ],
            'polygon with negative coordinates' => [
                'polygonValueObject' => new PolygonValueObject('((-1,-2),(-3,-4),(-5,-6))'),
                'postgresValue' => '((-1,-2),(-3,-4),(-5,-6))',
            ],
        ];
    }

    #[DataProvider('provideInvalidPHPValueInputs')]
    #[Test]
    public function throws_exception_for_invalid_php_value_inputs(mixed $phpValue): void
    {
        $this->expectException(InvalidPolygonForPHPException::class);
        $this->fixture->convertToDatabaseValue($phpValue, $this->platform);
    }

    /**
     * @return array<string, array{mixed}>
     */
    public static function provideInvalidPHPValueInputs(): array
    {
        return [
            'string input' => ['((0,0),(1,0),(0,1))'],
            'integer input' => [123],
            'array input' => [['not', 'polygon']],
            'boolean input' => [true],
            'object input' => [new \stdClass()],
        ];
    }

    #[DataProvider('provideInvalidDatabaseValueInputs')]
    #[Test]
    public function throws_exception_for_invalid_database_value_inputs(mixed $dbValue): void
    {
        $this->expectException(InvalidPolygonForDatabaseException::class);
        $this->fixture->convertToPHPValue($dbValue, $this->platform);
    }

    /**
     * @return array<string, array{mixed}>
     */
    public static function provideInvalidDatabaseValueInputs(): array
    {
        return [
            'empty string' => [''],
            'invalid format' => ['not a polygon'],
            'single point' => ['((1,2))'],
            'integer input' => [123],
            'array input' => [['not', 'polygon']],
            'boolean input' => [false],
            'object input' => [new \stdClass()],
        ];
    }
}
