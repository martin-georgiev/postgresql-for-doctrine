<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidTsqueryForDatabaseException;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidTsqueryForPHPException;
use MartinGeorgiev\Doctrine\DBAL\Types\Tsquery;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class TsqueryTest extends TestCase
{
    /**
     * @var AbstractPlatform&MockObject
     */
    private MockObject $platform;

    private Tsquery $fixture;

    protected function setUp(): void
    {
        $this->platform = $this->createMock(AbstractPlatform::class);
        $this->fixture = new Tsquery();
    }

    #[Test]
    public function has_name(): void
    {
        $this->assertEquals('tsquery', $this->fixture->getName());
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
            'single term' => [
                'phpValue' => 'cat',
                'databaseValue' => 'cat',
            ],
            'AND query' => [
                'phpValue' => 'fat & rat',
                'databaseValue' => 'fat & rat',
            ],
            'OR query' => [
                'phpValue' => 'cat | dog',
                'databaseValue' => 'cat | dog',
            ],
            'NOT query' => [
                'phpValue' => '!cat',
                'databaseValue' => '!cat',
            ],
            'phrase search' => [
                'phpValue' => "'fat' <-> 'cat'",
                'databaseValue' => "'fat' <-> 'cat'",
            ],
            'complex query' => [
                'phpValue' => "'fat' & ( 'cat' | 'rat' )",
                'databaseValue' => "'fat' & ( 'cat' | 'rat' )",
            ],
        ];
    }

    #[DataProvider('provideInvalidDatabaseValueInputs')]
    #[Test]
    public function throws_exception_for_invalid_database_value_inputs(mixed $value): void
    {
        $this->expectException(InvalidTsqueryForPHPException::class);

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
        $this->expectException(InvalidTsqueryForDatabaseException::class);

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
