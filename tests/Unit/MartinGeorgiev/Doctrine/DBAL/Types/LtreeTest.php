<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidLtreeForDatabaseException;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidLtreeForPHPException;
use MartinGeorgiev\Doctrine\DBAL\Types\Ltree;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Ltree as LtreeValueObject;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class LtreeTest extends TestCase
{
    private MockObject&PostgreSQLPlatform $platform;

    private Ltree $fixture;

    protected function setUp(): void
    {
        $this->platform = $this->createMock(PostgreSQLPlatform::class);

        $this->fixture = new Ltree();
    }

    #[Test]
    public function has_name(): void
    {
        self::assertSame('ltree', $this->fixture->getName());
    }

    #[Test]
    public function can_convert_string_to_database_value(): void
    {
        $value = 'alpha.beta.gamma';
        $databaseValue = $this->fixture->convertToDatabaseValue($value, $this->platform);

        self::assertSame($value, $databaseValue);
    }

    #[DataProvider('provideValidTransformations')]
    #[Test]
    public function can_transform_from_php_value(?LtreeValueObject $ltreeValueObject, ?string $postgresValue): void
    {
        self::assertSame($postgresValue, $this->fixture->convertToDatabaseValue($ltreeValueObject, $this->platform));
    }

    #[DataProvider('provideValidTransformations')]
    #[Test]
    public function can_transform_to_php_value(?LtreeValueObject $ltreeValueObject, ?string $postgresValue): void
    {
        self::assertEquals($ltreeValueObject, $this->fixture->convertToPHPValue($postgresValue, $this->platform));
    }

    /**
     * @return array<string, array{ltreeValueObject: LtreeValueObject|null, postgresValue: string|null}>
     */
    public static function provideValidTransformations(): array
    {
        return [
            'null' => [
                'ltreeValueObject' => null,
                'postgresValue' => null,
            ],
            'valid empty ltree' => [
                'ltreeValueObject' => new LtreeValueObject([]),
                'postgresValue' => '',
            ],
            'valid numeric ltree' => [
                'ltreeValueObject' => new LtreeValueObject(['1', '2', '3']),
                'postgresValue' => '1.2.3',
            ],
            'valid string ltree' => [
                'ltreeValueObject' => new LtreeValueObject(['alpha', 'beta', 'gamma']),
                'postgresValue' => 'alpha.beta.gamma',
            ],
        ];
    }

    #[DataProvider('provideInvalidDatabaseValueInputs')]
    #[Test]
    public function throws_exception_for_invalid_database_value_inputs(mixed $phpValue): void
    {
        $this->expectException(InvalidLtreeForPHPException::class);
        $this->fixture->convertToDatabaseValue($phpValue, $this->platform);
    }

    /**
     * @return array<string, array{mixed}>
     */
    public static function provideInvalidDatabaseValueInputs(): array
    {
        return [
            'invalid string' => ['invalid..ltree'],
            'integer input' => [123],
            'array input' => [['not', 'ltree']],
            'boolean input' => [true],
            'object input' => [new \stdClass()],
        ];
    }

    #[DataProvider('provideInvalidPHPValueInputs')]
    #[Test]
    public function throws_exception_for_invalid_php_value_inputs(mixed $phpValue): void
    {
        $this->expectException(InvalidLtreeForDatabaseException::class);
        $this->fixture->convertToPHPValue($phpValue, $this->platform);
    }

    /**
     * @return array<string, array{mixed}>
     */
    public static function provideInvalidPHPValueInputs(): array
    {
        return [
            'starting by dot' => ['.root'],
            'ending by dot' => ['root.'],
            'empty dots' => ['a..b'],
            'not a string' => [123],
            'array input' => [['not', 'point']],
            'boolean input' => [false],
            'object input' => [new \stdClass()],
        ];
    }
}
