<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidPointForDatabaseException;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidPointForPHPException;
use MartinGeorgiev\Doctrine\DBAL\Types\Point;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Point as PointValueObject;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class PointTest extends TestCase
{
    /**
     * @var AbstractPlatform&MockObject
     */
    private MockObject $platform;

    private Point $fixture;

    protected function setUp(): void
    {
        $this->platform = $this->createMock(AbstractPlatform::class);
        $this->fixture = new Point();
    }

    #[Test]
    public function has_name(): void
    {
        $this->assertSame('point', $this->fixture->getName());
    }

    #[DataProvider('provideValidTransformations')]
    #[Test]
    public function can_transform_from_php_value(?PointValueObject $pointValueObject, ?string $postgresValue): void
    {
        $this->assertSame($postgresValue, $this->fixture->convertToDatabaseValue($pointValueObject, $this->platform));
    }

    #[DataProvider('provideValidTransformations')]
    #[Test]
    public function can_transform_to_php_value(?PointValueObject $pointValueObject, ?string $postgresValue): void
    {
        $this->assertEquals($pointValueObject, $this->fixture->convertToPHPValue($postgresValue, $this->platform));
    }

    /**
     * @return array<string, array{pointValueObject: PointValueObject|null, postgresValue: string|null}>
     */
    public static function provideValidTransformations(): array
    {
        return [
            'null' => [
                'pointValueObject' => null,
                'postgresValue' => null,
            ],
            'valid point' => [
                'pointValueObject' => new PointValueObject(1.23, 4.56),
                'postgresValue' => '(1.23,4.56)',
            ],
            'negative coordinates' => [
                'pointValueObject' => new PointValueObject(-1.23, -4.56),
                'postgresValue' => '(-1.23,-4.56)',
            ],
            'zero coordinates' => [
                'pointValueObject' => new PointValueObject(0.0, 0.0),
                'postgresValue' => '(0,0)',
            ],
            'high precision coordinates' => [
                'pointValueObject' => new PointValueObject(45.123456789, 179.987654321),
                'postgresValue' => '(45.123456789,179.987654321)',
            ],
        ];
    }

    #[DataProvider('provideInvalidDatabaseValueInputs')]
    #[Test]
    public function throws_exception_for_invalid_database_value_inputs(mixed $phpValue): void
    {
        $this->expectException(InvalidPointForPHPException::class);
        $this->fixture->convertToDatabaseValue($phpValue, $this->platform);
    }

    /**
     * @return array<string, array{mixed}>
     */
    public static function provideInvalidDatabaseValueInputs(): array
    {
        return [
            'empty string' => [''],
            'whitespace string' => [' '],
            'invalid format' => ['invalid point'],
            'integer input' => [123],
            'array input' => [['not', 'point']],
            'boolean input' => [true],
            'object input' => [new \stdClass()],
        ];
    }

    #[DataProvider('provideInvalidPHPValueInputs')]
    #[Test]
    public function throws_exception_for_invalid_php_value_inputs(mixed $phpValue): void
    {
        $this->expectException(InvalidPointForDatabaseException::class);
        $this->fixture->convertToPHPValue($phpValue, $this->platform);
    }

    /**
     * @return array<string, array{mixed}>
     */
    public static function provideInvalidPHPValueInputs(): array
    {
        return [
            'empty string' => [''],
            'whitespace string' => [' '],
            'invalid format' => ['1.23,4.56'],
            'missing parentheses' => ['1.23,4.56)'],
            'non-numeric values' => ['(a,b)'],
            'too many coordinates' => ['(1.23,4.56,7.89)'],
            'not a string' => [123],
            'array input' => [['not', 'point']],
            'boolean input' => [false],
            'object input' => [new \stdClass()],
        ];
    }
}
