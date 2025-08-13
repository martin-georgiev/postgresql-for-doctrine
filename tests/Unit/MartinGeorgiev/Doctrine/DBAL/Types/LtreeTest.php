<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\ParameterType;
use Doctrine\DBAL\Platforms\AbstractPlatform;
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

    public function test_get_sql_declaration(): void
    {
        self::assertSame('ltree', $this->fixture->getSQLDeclaration([], $this->platform));
    }

    public function test_get_binding_type(): void
    {
        self::assertSame(ParameterType::STRING, $this->fixture->getBindingType());
    }

    public function test_get_mapped_database_types(): void
    {
        self::assertSame(['ltree'], $this->fixture->getMappedDatabaseTypes($this->platform));
    }

    public function test_convert_to_database_value_accepts_string(): void
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

    public function test_get_sql_declaration_throws_on_non_postgresql_platform(): void
    {
        $this->expectException(\LogicException::class);
        $this->fixture->getSQLDeclaration([], self::createStub(AbstractPlatform::class));
    }

    public function test_get_mapped_database_types_throws_on_non_postgresql_platform(): void
    {
        $this->expectException(\LogicException::class);
        $this->fixture->getMappedDatabaseTypes(self::createStub(AbstractPlatform::class));
    }

    public function test_convert_to_database_value_throws_on_non_postgresql_platform(): void
    {
        $this->expectException(\LogicException::class);
        $this->fixture->convertToDatabaseValue(
            new LtreeValueObject(['valid', 'ltree']),
            self::createStub(AbstractPlatform::class),
        );
    }

    public function test_convert_to_php_value_throws_on_non_postgresql_platform(): void
    {
        $this->expectException(\LogicException::class);
        $this->fixture->convertToPHPValue('valid.ltree', self::createStub(AbstractPlatform::class));
    }
}
