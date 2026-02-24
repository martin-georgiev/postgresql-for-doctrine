<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidTsvectorForDatabaseException;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidTsvectorForPHPException;
use MartinGeorgiev\Doctrine\DBAL\Types\Tsvector;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class TsvectorTest extends TestCase
{
    /**
     * @var AbstractPlatform&MockObject
     */
    private MockObject $platform;

    private Tsvector $fixture;

    protected function setUp(): void
    {
        $this->platform = $this->createMock(AbstractPlatform::class);
        $this->fixture = new Tsvector();
    }

    #[Test]
    public function has_name(): void
    {
        $this->assertEquals('tsvector', $this->fixture->getName());
    }

    #[DataProvider('provideValidTransformations')]
    #[Test]
    public function can_transform_from_php_value(?string $phpValue, ?string $databaseValue): void
    {
        $this->assertEquals($databaseValue, $this->fixture->convertToDatabaseValue($phpValue, $this->platform));
    }

    #[DataProvider('provideValidTransformations')]
    #[Test]
    public function can_transform_to_php_value(?string $phpValue, ?string $databaseValue): void
    {
        $this->assertEquals($databaseValue, $this->fixture->convertToPHPValue($databaseValue, $this->platform));
    }

    /**
     * @return array<string, array{phpValue: string|null, databaseValue: string|null}>
     */
    public static function provideValidTransformations(): array
    {
        return [
            'null' => [
                'phpValue' => null,
                'databaseValue' => null,
            ],
            'single lexeme' => [
                'phpValue' => 'quick',
                'databaseValue' => 'quick',
            ],
            'multiple lexemes' => [
                'phpValue' => "'fat' 'rat' 'sat'",
                'databaseValue' => "'fat' 'rat' 'sat'",
            ],
            'lexemes with positions' => [
                'phpValue' => "'cat':3 'fat':2 'sat':1",
                'databaseValue' => "'cat':3 'fat':2 'sat':1",
            ],
            'lexemes with weights' => [
                'phpValue' => "'important':1A 'normal':2",
                'databaseValue' => "'important':1A 'normal':2",
            ],
        ];
    }

    #[DataProvider('provideInvalidDatabaseValueInputs')]
    #[Test]
    public function throws_exception_for_invalid_database_value_inputs(mixed $value): void
    {
        $this->expectException(InvalidTsvectorForPHPException::class);

        $this->fixture->convertToDatabaseValue($value, $this->platform);
    }

    /**
     * @return array<string, array{mixed}>
     */
    public static function provideInvalidDatabaseValueInputs(): array
    {
        return [
            'empty string' => [''],
            'integer input' => [42],
            'array input' => [['not', 'a', 'string']],
            'boolean input' => [true],
            'float input' => [3.14],
            'object input' => [new \stdClass()],
        ];
    }

    #[DataProvider('provideInvalidPHPValueInputs')]
    #[Test]
    public function throws_exception_for_invalid_php_value_inputs(mixed $value): void
    {
        $this->expectException(InvalidTsvectorForDatabaseException::class);

        $this->fixture->convertToPHPValue($value, $this->platform);
    }

    /**
     * @return array<string, array{mixed}>
     */
    public static function provideInvalidPHPValueInputs(): array
    {
        return [
            'integer input' => [42],
            'array input' => [['not', 'a', 'string']],
            'boolean input' => [true],
            'float input' => [3.14],
            'object input' => [new \stdClass()],
        ];
    }
}
