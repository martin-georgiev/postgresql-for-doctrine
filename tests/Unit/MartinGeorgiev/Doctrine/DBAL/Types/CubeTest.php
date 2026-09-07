<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use MartinGeorgiev\Doctrine\DBAL\Types\Cube;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidCubeForDatabaseException;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidCubeForPHPException;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Cube as CubeValueObject;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;

final class CubeTest extends TestCase
{
    /**
     * @var AbstractPlatform&Stub
     */
    private Stub $platform;

    private Cube $fixture;

    protected function setUp(): void
    {
        $this->platform = $this->createStub(AbstractPlatform::class);
        $this->fixture = new Cube();
    }

    #[Test]
    public function has_name(): void
    {
        $this->assertSame('cube', $this->fixture->getName());
    }

    #[Test]
    public function converts_null_to_database_value(): void
    {
        $this->assertNull($this->fixture->convertToDatabaseValue(null, $this->platform));
    }

    #[Test]
    public function converts_null_to_php_value(): void
    {
        $this->assertNull($this->fixture->convertToPHPValue(null, $this->platform));
    }

    #[DataProvider('provideValidTransformations')]
    #[Test]
    public function converts_to_database_value(CubeValueObject $cubeValueObject, string $postgresValue): void
    {
        $this->assertSame($postgresValue, $this->fixture->convertToDatabaseValue($cubeValueObject, $this->platform));
    }

    #[DataProvider('provideValidTransformations')]
    #[Test]
    public function converts_to_php_value(CubeValueObject $cubeValueObject, string $postgresValue): void
    {
        $this->assertEquals($cubeValueObject, $this->fixture->convertToPHPValue($postgresValue, $this->platform));
    }

    /**
     * @return array<string, array{cubeValueObject: CubeValueObject, postgresValue: string}>
     */
    public static function provideValidTransformations(): array
    {
        return [
            'point' => [
                'cubeValueObject' => CubeValueObject::point(1.0, 2.0, 3.0),
                'postgresValue' => '(1, 2, 3)',
            ],
            'box' => [
                'cubeValueObject' => new CubeValueObject([1.0, 2.0, 3.0], [4.0, 5.0, 6.0]),
                'postgresValue' => '(1, 2, 3),(4, 5, 6)',
            ],
            'one-dimensional point' => [
                'cubeValueObject' => CubeValueObject::point(42.0),
                'postgresValue' => '(42)',
            ],
            'negative coordinates' => [
                'cubeValueObject' => new CubeValueObject([-1.5, -2.5], [-0.5, -0.25]),
                'postgresValue' => '(-1.5, -2.5),(-0.5, -0.25)',
            ],
        ];
    }

    #[DataProvider('provideInvalidDatabaseValueInputs')]
    #[Test]
    public function throws_exception_for_invalid_database_value_inputs(mixed $phpValue): void
    {
        $this->expectException(InvalidCubeForDatabaseException::class);
        $this->fixture->convertToDatabaseValue($phpValue, $this->platform);
    }

    /**
     * @return array<string, array{mixed}>
     */
    public static function provideInvalidDatabaseValueInputs(): array
    {
        return [
            'string input' => ['(1, 2, 3)'],
            'integer input' => [123],
            'array input' => [[1, 2, 3]],
            'boolean input' => [true],
            'object input' => [new \stdClass()],
        ];
    }

    #[DataProvider('provideInvalidPHPValueInputs')]
    #[Test]
    public function throws_exception_for_invalid_php_value_inputs(mixed $databaseValue): void
    {
        $this->expectException(InvalidCubeForPHPException::class);
        $this->fixture->convertToPHPValue($databaseValue, $this->platform);
    }

    /**
     * @return array<string, array{mixed}>
     */
    public static function provideInvalidPHPValueInputs(): array
    {
        return [
            'empty string' => [''],
            'empty parentheses' => ['()'],
            'invalid format' => ['not a cube'],
            'mismatched dimensions' => ['(1,2),(3)'],
            'non-finite coordinate' => ['(NaN, 1)'],
            'integer input' => [123],
            'array input' => [[1, 2, 3]],
            'boolean input' => [false],
            'object input' => [new \stdClass()],
        ];
    }
}
