<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidVectorForDatabaseException;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidVectorForPHPException;
use MartinGeorgiev\Doctrine\DBAL\Types\Vector;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class VectorTest extends TestCase
{
    /**
     * @var AbstractPlatform&MockObject
     */
    private MockObject $platform;

    private Vector $fixture;

    protected function setUp(): void
    {
        $this->platform = $this->createMock(AbstractPlatform::class);
        $this->fixture = new Vector();
    }

    #[Test]
    public function has_name(): void
    {
        $this->assertSame('vector', $this->fixture->getName());
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
        $this->expectException(InvalidVectorForDatabaseException::class);
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
        ];
    }

    #[Test]
    public function throws_exception_for_non_numeric_array_item(): void
    {
        $this->expectException(InvalidVectorForDatabaseException::class);
        $this->fixture->convertToDatabaseValue(['not', 'floats'], $this->platform);
    }

    #[DataProvider('provideInvalidPHPInputs')]
    #[Test]
    public function throws_exception_for_invalid_php_value(mixed $postgresValue): void
    {
        $this->expectException(InvalidVectorForPHPException::class);
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
    public function throws_exception_for_invalid_vector_format(): void
    {
        $this->expectException(InvalidVectorForPHPException::class);
        $this->fixture->convertToPHPValue('[0.1,not-a-number,0.3]', $this->platform);
    }
}
