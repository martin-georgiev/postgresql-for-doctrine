<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidPointForDatabaseException;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidPointForPHPException;
use MartinGeorgiev\Doctrine\DBAL\Types\Point;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Point as PointValueObject;
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

    /**
     * @test
     */
    public function has_name(): void
    {
        self::assertEquals('point', $this->fixture->getName());
    }

    /**
     * @test
     *
     * @dataProvider provideValidTransformations
     */
    public function can_transform_from_php_value(?PointValueObject $pointValueObject, ?string $postgresValue): void
    {
        self::assertEquals($postgresValue, $this->fixture->convertToDatabaseValue($pointValueObject, $this->platform));
    }

    /**
     * @test
     *
     * @dataProvider provideValidTransformations
     */
    public function can_transform_to_php_value(?PointValueObject $pointValueObject, ?string $postgresValue): void
    {
        self::assertEquals($pointValueObject, $this->fixture->convertToPHPValue($postgresValue, $this->platform));
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
                'postgresValue' => '(1.230000, 4.560000)',
            ],
            'negative coordinates' => [
                'pointValueObject' => new PointValueObject(-1.23, -4.56),
                'postgresValue' => '(-1.230000, -4.560000)',
            ],
            'zero coordinates' => [
                'pointValueObject' => new PointValueObject(0.0, 0.0),
                'postgresValue' => '(0.000000, 0.000000)',
            ],
            'maximum float precision' => [
                'pointValueObject' => new PointValueObject(45.123456, 179.987654),
                'postgresValue' => '(45.123456, 179.987654)',
            ],
        ];
    }

    /**
     * @test
     *
     * @dataProvider provideInvalidTransformations
     */
    public function throws_exception_when_invalid_data_provided_to_convert_to_database_value(mixed $phpValue): void
    {
        $this->expectException(InvalidPointForPHPException::class);
        $this->fixture->convertToDatabaseValue($phpValue, $this->platform);
    }

    /**
     * @return array<string, array{mixed}>
     */
    public static function provideInvalidTransformations(): array
    {
        return [
            'empty string' => [''],
            'whitespace string' => [' '],
            'invalid format' => ['invalid point'],
        ];
    }

    /**
     * @test
     *
     * @dataProvider provideInvalidDatabaseValues
     */
    public function throws_exception_when_invalid_data_provided_to_convert_to_php_value(mixed $phpValue): void
    {
        $this->expectException(InvalidPointForDatabaseException::class);
        $this->fixture->convertToPHPValue($phpValue, $this->platform);
    }

    /**
     * @return array<string, array{mixed}>
     */
    public static function provideInvalidDatabaseValues(): array
    {
        return [
            'empty string' => [''],
            'whitespace string' => [' '],
            'invalid format' => ['1.23,4.56'],
            'missing parentheses' => ['1.23,4.56)'],
            'non-numeric values' => ['(a,b)'],
            'too many coordinates' => ['(1.23,4.56,7.89)'],
            'not a string' => [123],
            'float precision is too granular' => ['(1.23456789,7.89)'],
        ];
    }
}
