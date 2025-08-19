<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidGeographyForDatabaseException;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidGeographyForPHPException;
use MartinGeorgiev\Doctrine\DBAL\Types\Geography;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\WktSpatialData;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class GeographyTest extends TestCase
{
    /**
     * @var AbstractPlatform&MockObject
     */
    private MockObject $platform;

    private Geography $fixture;

    protected function setUp(): void
    {
        $this->platform = $this->createMock(AbstractPlatform::class);
        $this->fixture = new Geography();
    }

    #[Test]
    public function has_name(): void
    {
        self::assertEquals('geography', $this->fixture->getName());
    }

    #[DataProvider('provideValidTransformations')]
    #[Test]
    public function can_transform_from_php_value(?WktSpatialData $wktSpatialData, ?string $postgresValue): void
    {
        self::assertEquals($postgresValue, $this->fixture->convertToDatabaseValue($wktSpatialData, $this->platform));
    }

    #[DataProvider('provideValidTransformations')]
    #[Test]
    public function can_transform_to_php_value(?WktSpatialData $wktSpatialData, ?string $postgresValue): void
    {
        $result = $this->fixture->convertToPHPValue($postgresValue, $this->platform);
        if (!$wktSpatialData instanceof WktSpatialData) {
            self::assertNull($result);

            return;
        }

        self::assertInstanceOf(WktSpatialData::class, $result);
        self::assertEquals((string) $wktSpatialData, (string) $result);
    }

    /**
     * @return array<string, array{valueObject: WktSpatialData|null, postgresValue: string|null}>
     */
    public static function provideValidTransformations(): array
    {
        return [
            'null' => [
                'valueObject' => null,
                'postgresValue' => null,
            ],
            'point' => [
                'valueObject' => WktSpatialData::fromWkt('POINT(1 2)'),
                'postgresValue' => 'POINT(1 2)',
            ],
        ];
    }

    #[DataProvider('provideInvalidDatabaseValueInputs')]
    #[Test]
    public function throws_exception_for_invalid_database_value_inputs(mixed $phpValue): void
    {
        $this->expectException(InvalidGeographyForPHPException::class);
        $this->fixture->convertToDatabaseValue($phpValue, $this->platform);
    }

    /**
     * @return array<string, array{mixed}>
     */
    public static function provideInvalidDatabaseValueInputs(): array
    {
        return [
            'random string' => ['foo'],
            'integer input' => [123],
            'array input' => [['not', 'geography']],
            'boolean input' => [true],
            'object input' => [new \stdClass()],
        ];
    }

    #[DataProvider('provideInvalidPHPValueInputs')]
    #[Test]
    public function throws_exception_for_invalid_php_value_inputs(mixed $postgresValue): void
    {
        $this->expectException(InvalidGeographyForDatabaseException::class);
        $this->fixture->convertToPHPValue($postgresValue, $this->platform);
    }

    /**
     * @return array<string, array{mixed}>
     */
    public static function provideInvalidPHPValueInputs(): array
    {
        return [
            'empty string' => [''],
            'invalid wkt' => ['INVALID_WKT'],
            'missing coordinates' => ['POINT()'],
            'not a string' => [123],
        ];
    }
}
