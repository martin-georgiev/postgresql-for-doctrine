<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Fixtures\MartinGeorgiev\Doctrine\Colors;
use Fixtures\MartinGeorgiev\Doctrine\ConcreteColorType;
use Fixtures\MartinGeorgiev\Doctrine\ConcreteInvalidlyNamedColorType;
use Fixtures\MartinGeorgiev\Doctrine\ConcretePriorityType;
use Fixtures\MartinGeorgiev\Doctrine\ConcreteShadeType;
use Fixtures\MartinGeorgiev\Doctrine\Sizes;
use MartinGeorgiev\Doctrine\DBAL\Types\Enum;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidEnumDefinitionException;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidEnumForDatabaseException;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidEnumForPHPException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;

final class EnumTest extends TestCase
{
    /**
     * @var AbstractPlatform&Stub
     */
    private Stub $platform;

    private ConcreteColorType $fixture;

    protected function setUp(): void
    {
        $this->platform = $this->createStub(AbstractPlatform::class);
        $this->fixture = new ConcreteColorType();
    }

    #[Test]
    public function has_name(): void
    {
        $this->assertSame('test_color', $this->fixture->getName());
    }

    #[Test]
    public function returns_sql_declaration_as_type_name(): void
    {
        $this->assertSame('test_color', $this->fixture->getSQLDeclaration([], $this->platform));
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

    #[Test]
    public function converts_empty_string_from_database_to_null(): void
    {
        $this->assertNull($this->fixture->convertToPHPValue('', $this->platform));
    }

    #[Test]
    public function converts_backed_enum_to_database_value(): void
    {
        $this->assertSame('red', $this->fixture->convertToDatabaseValue(Colors::RED, $this->platform));
    }

    #[Test]
    public function converts_database_string_to_backed_enum(): void
    {
        $this->assertSame(Colors::BLUE, $this->fixture->convertToPHPValue('blue', $this->platform));
    }

    #[DataProvider('provideNonBackedEnumValues')]
    #[Test]
    public function throws_exception_for_non_backed_enum_in_database_value(mixed $value): void
    {
        $this->expectException(InvalidEnumForDatabaseException::class);

        $this->fixture->convertToDatabaseValue($value, $this->platform);
    }

    /**
     * @return array<string, array{mixed}>
     */
    public static function provideNonBackedEnumValues(): array
    {
        return [
            'integer' => [42],
            'string' => ['red'],
            'array' => [['red']],
            'object' => [new \stdClass()],
            'boolean' => [true],
        ];
    }

    #[Test]
    public function throws_exception_for_wrong_enum_class_in_database_value(): void
    {
        $this->expectException(InvalidEnumForDatabaseException::class);

        $this->fixture->convertToDatabaseValue(Sizes::SMALL, $this->platform);
    }

    #[DataProvider('provideNonStringPhpValues')]
    #[Test]
    public function throws_exception_for_non_string_in_php_value(mixed $value): void
    {
        $this->expectException(InvalidEnumForPHPException::class);

        $this->fixture->convertToPHPValue($value, $this->platform);
    }

    /**
     * @return array<string, array{mixed}>
     */
    public static function provideNonStringPhpValues(): array
    {
        return [
            'integer' => [42],
            'array' => [['red']],
            'object' => [new \stdClass()],
            'boolean' => [true],
        ];
    }

    #[Test]
    public function throws_exception_for_unknown_enum_value_in_php_value(): void
    {
        $this->expectException(InvalidEnumForPHPException::class);

        $this->fixture->convertToPHPValue('purple', $this->platform);
    }

    #[Test]
    public function throws_exception_for_non_backed_enum_class_in_php_value(): void
    {
        $type = new class extends Enum {
            protected const TYPE_NAME = 'test_non_backed';

            protected function getEnumClass(): string
            {
                return \stdClass::class; // @phpstan-ignore-line
            }
        };

        $this->expectException(InvalidEnumForPHPException::class);

        $type->convertToPHPValue('any_value', $this->platform);
    }

    #[Test]
    public function creates_type_sql_from_php_enum_cases(): void
    {
        $this->assertSame(
            'CREATE TYPE "test_color" AS ENUM (\'red\', \'blue\')',
            $this->fixture->getCreateTypeSQL()
        );
    }

    #[Test]
    public function creates_type_sql_for_schema_qualified_type_name(): void
    {
        $concreteShadeType = new ConcreteShadeType();

        $this->assertSame(
            'CREATE TYPE "test"."test_shade" AS ENUM (\'pale\', \'robin\'\'s egg\')',
            $concreteShadeType->getCreateTypeSQL()
        );
    }

    #[Test]
    public function drops_type_sql(): void
    {
        $this->assertSame('DROP TYPE "test_color"', $this->fixture->getDropTypeSQL());
    }

    #[Test]
    public function drops_type_sql_for_schema_qualified_type_name(): void
    {
        $concreteShadeType = new ConcreteShadeType();

        $this->assertSame('DROP TYPE "test"."test_shade"', $concreteShadeType->getDropTypeSQL());
    }

    #[Test]
    public function throws_exception_for_int_backed_enum_in_create_type_sql(): void
    {
        $concretePriorityType = new ConcretePriorityType();

        $this->expectException(InvalidEnumDefinitionException::class);

        $concretePriorityType->getCreateTypeSQL();
    }

    #[Test]
    public function throws_exception_for_unsafe_type_name_in_create_type_sql(): void
    {
        $concreteInvalidlyNamedColorType = new ConcreteInvalidlyNamedColorType();

        $this->expectException(InvalidEnumDefinitionException::class);

        $concreteInvalidlyNamedColorType->getCreateTypeSQL();
    }

    #[Test]
    public function throws_exception_for_unsafe_type_name_in_drop_type_sql(): void
    {
        $concreteInvalidlyNamedColorType = new ConcreteInvalidlyNamedColorType();

        $this->expectException(InvalidEnumDefinitionException::class);

        $concreteInvalidlyNamedColorType->getDropTypeSQL();
    }

    #[DataProvider('provideInvalidTypeNames')]
    #[Test]
    public function throws_exception_for_invalid_type_name(string $typeName): void
    {
        $type = new class($typeName) extends Enum {
            public function __construct(private readonly string $configuredTypeName) {}

            public function getName(): string
            {
                return $this->configuredTypeName;
            }

            protected function getEnumClass(): string
            {
                return Colors::class;
            }
        };

        $this->expectException(InvalidEnumDefinitionException::class);

        $type->getCreateTypeSQL();
    }

    /**
     * @return array<string, array{string}>
     */
    public static function provideInvalidTypeNames(): array
    {
        return [
            'more than two segments' => ['db.test.test_color'],
            'empty segment' => ['test.'],
            'leading digit' => ['1_color'],
            'hyphen' => ['test-color'],
            'whitespace' => ['test color'],
            'embedded quote' => ['test"color'],
        ];
    }
}
