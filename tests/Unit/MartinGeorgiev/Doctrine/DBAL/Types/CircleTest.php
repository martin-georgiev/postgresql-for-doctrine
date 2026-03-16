<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use MartinGeorgiev\Doctrine\DBAL\Types\Circle;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidCircleForDatabaseException;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidCircleForPHPException;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Circle as CircleValueObject;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class CircleTest extends TestCase
{
    /**
     * @var AbstractPlatform&MockObject
     */
    private MockObject $platform;

    private Circle $fixture;

    protected function setUp(): void
    {
        $this->platform = $this->createMock(AbstractPlatform::class);
        $this->fixture = new Circle();
    }

    #[Test]
    public function has_name(): void
    {
        $this->assertSame('circle', $this->fixture->getName());
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
    public function can_transform_from_php_value(CircleValueObject $circleValueObject, string $postgresValue): void
    {
        $this->assertSame($postgresValue, $this->fixture->convertToDatabaseValue($circleValueObject, $this->platform));
    }

    #[DataProvider('provideValidTransformations')]
    #[Test]
    public function can_transform_to_php_value(CircleValueObject $circleValueObject, string $postgresValue): void
    {
        $result = $this->fixture->convertToPHPValue($postgresValue, $this->platform);
        $this->assertInstanceOf(CircleValueObject::class, $result);
        $this->assertSame($postgresValue, (string) $result);
    }

    /**
     * @return array<string, array{circleValueObject: CircleValueObject, postgresValue: string}>
     */
    public static function provideValidTransformations(): array
    {
        return [
            'basic circle' => [
                'circleValueObject' => new CircleValueObject('<(1,2),3>'),
                'postgresValue' => '<(1,2),3>',
            ],
            'circle with floats' => [
                'circleValueObject' => new CircleValueObject('<(1.5,2.5),3.5>'),
                'postgresValue' => '<(1.5,2.5),3.5>',
            ],
            'circle with negative center' => [
                'circleValueObject' => new CircleValueObject('<(-1,-2),5>'),
                'postgresValue' => '<(-1,-2),5>',
            ],
            'circle at origin' => [
                'circleValueObject' => new CircleValueObject('<(0,0),1>'),
                'postgresValue' => '<(0,0),1>',
            ],
        ];
    }

    #[DataProvider('provideInvalidPHPValueInputs')]
    #[Test]
    public function throws_exception_for_invalid_php_value_inputs(mixed $phpValue): void
    {
        $this->expectException(InvalidCircleForPHPException::class);
        $this->fixture->convertToDatabaseValue($phpValue, $this->platform);
    }

    /**
     * @return array<string, array{mixed}>
     */
    public static function provideInvalidPHPValueInputs(): array
    {
        return [
            'string input' => ['<(1,2),3>'],
            'integer input' => [123],
            'array input' => [['not', 'circle']],
            'boolean input' => [true],
            'object input' => [new \stdClass()],
        ];
    }

    #[DataProvider('provideInvalidDatabaseValueInputs')]
    #[Test]
    public function throws_exception_for_invalid_database_value_inputs(mixed $dbValue): void
    {
        $this->expectException(InvalidCircleForDatabaseException::class);
        $this->fixture->convertToPHPValue($dbValue, $this->platform);
    }

    /**
     * @return array<string, array{mixed}>
     */
    public static function provideInvalidDatabaseValueInputs(): array
    {
        return [
            'empty string' => [''],
            'invalid format' => ['not a circle'],
            'missing angle brackets' => ['(1,2),3'],
            'integer input' => [123],
            'array input' => [['not', 'circle']],
            'boolean input' => [false],
            'object input' => [new \stdClass()],
        ];
    }
}
