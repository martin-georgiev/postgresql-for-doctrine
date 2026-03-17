<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use MartinGeorgiev\Doctrine\DBAL\Types\BaseType;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

abstract class BaseVectorTypeTestCase extends TestCase
{
    /**
     * @var AbstractPlatform&MockObject
     */
    protected MockObject $platform;

    protected BaseType $fixture;

    abstract protected function getExpectedTypeName(): string;

    abstract protected function createFixture(): BaseType;

    /**
     * @return class-string<ConversionException>
     */
    abstract protected function getDatabaseExceptionClass(): string;

    /**
     * @return class-string<ConversionException>
     */
    abstract protected function getPHPExceptionClass(): string;

    protected function setUp(): void
    {
        $this->platform = $this->createMock(AbstractPlatform::class);
        $this->fixture = $this->createFixture();
    }

    #[Test]
    public function has_name(): void
    {
        $this->assertSame($this->getExpectedTypeName(), $this->fixture->getName());
    }

    #[DataProvider('provideValidPHPToDatabase')]
    #[Test]
    public function can_transform_from_php_value(mixed $phpValue, ?string $postgresValue): void
    {
        $this->assertSame($postgresValue, $this->fixture->convertToDatabaseValue($phpValue, $this->platform));
    }

    /**
     * @return array<string, array{phpValue: list<float>|null, postgresValue: string|null}>
     */
    public static function provideValidPHPToDatabase(): array
    {
        return [
            'null' => [
                'phpValue' => null,
                'postgresValue' => null,
            ],
            'empty vector' => [
                'phpValue' => [],
                'postgresValue' => '[]',
            ],
            'single element' => [
                'phpValue' => [1.0],
                'postgresValue' => '[1]',
            ],
            'multiple floats' => [
                'phpValue' => [0.1, 0.2, 0.3],
                'postgresValue' => '[0.1,0.2,0.3]',
            ],
            'integers are accepted' => [
                'phpValue' => [1, 2, 3],
                'postgresValue' => '[1,2,3]',
            ],
            'negative values' => [
                'phpValue' => [-1.5, 0.0, 1.5],
                'postgresValue' => '[-1.5,0,1.5]',
            ],
        ];
    }

    #[DataProvider('provideValidDatabaseToPHP')]
    #[Test]
    public function can_transform_to_php_value(?string $postgresValue, ?array $phpValue): void
    {
        $this->assertSame($phpValue, $this->fixture->convertToPHPValue($postgresValue, $this->platform));
    }

    /**
     * @return array<string, array{postgresValue: string|null, phpValue: list<float>|null}>
     */
    public static function provideValidDatabaseToPHP(): array
    {
        return [
            'null' => [
                'postgresValue' => null,
                'phpValue' => null,
            ],
            'empty vector' => [
                'postgresValue' => '[]',
                'phpValue' => [],
            ],
            'single element' => [
                'postgresValue' => '[1.0]',
                'phpValue' => [1.0],
            ],
            'multiple floats' => [
                'postgresValue' => '[0.1,0.2,0.3]',
                'phpValue' => [0.1, 0.2, 0.3],
            ],
            'negative values' => [
                'postgresValue' => '[-1.5,0,1.5]',
                'phpValue' => [-1.5, 0.0, 1.5],
            ],
        ];
    }

    #[DataProvider('provideInvalidDatabaseInputs')]
    #[Test]
    public function throws_exception_for_invalid_database_value(mixed $phpValue): void
    {
        $this->expectException($this->getDatabaseExceptionClass());
        $this->fixture->convertToDatabaseValue($phpValue, $this->platform);
    }

    /**
     * @return array<string, array{mixed}>
     */
    public static function provideInvalidDatabaseInputs(): array
    {
        return [
            'string input' => ['not an array'],
            'integer input' => [42],
            'boolean input' => [true],
            'object input' => [new \stdClass()],
            'non-list array' => [[2 => 0.5]],
        ];
    }

    #[Test]
    public function throws_exception_for_non_numeric_array_item(): void
    {
        $this->expectException($this->getDatabaseExceptionClass());
        $this->fixture->convertToDatabaseValue(['not', 'floats'], $this->platform);
    }

    #[DataProvider('provideInvalidPHPInputs')]
    #[Test]
    public function throws_exception_for_invalid_php_value(mixed $postgresValue): void
    {
        $this->expectException($this->getPHPExceptionClass());
        $this->fixture->convertToPHPValue($postgresValue, $this->platform);
    }

    /**
     * @return array<string, array{mixed}>
     */
    public static function provideInvalidPHPInputs(): array
    {
        return [
            'integer input' => [42],
            'array input' => [['not', 'a', 'string']],
            'boolean input' => [true],
            'object input' => [new \stdClass()],
        ];
    }

    #[Test]
    public function throws_exception_for_invalid_format(): void
    {
        $this->expectException($this->getPHPExceptionClass());
        $this->fixture->convertToPHPValue('[0.1,not-a-number,0.3]', $this->platform);
    }

    #[DataProvider('provideMalformedBracketInputs')]
    #[Test]
    public function throws_exception_for_malformed_brackets(string $input): void
    {
        $this->expectException($this->getPHPExceptionClass());
        $this->fixture->convertToPHPValue($input, $this->platform);
    }

    /**
     * @return array<string, array{string}>
     */
    public static function provideMalformedBracketInputs(): array
    {
        return [
            'missing closing bracket' => ['[1,2'],
            'missing opening bracket' => ['1,2]'],
            'no brackets' => ['1,2,3'],
            'single character' => ['['],
        ];
    }
}
