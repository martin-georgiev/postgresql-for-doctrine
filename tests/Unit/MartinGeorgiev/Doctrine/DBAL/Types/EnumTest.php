<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Fixtures\MartinGeorgiev\Doctrine\ConcreteTrickyLabelType;
use Fixtures\MartinGeorgiev\Doctrine\Sizes;
use Fixtures\MartinGeorgiev\Doctrine\TrickyLabels;
use MartinGeorgiev\Doctrine\DBAL\Types\Enum;
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

    private ConcreteTrickyLabelType $fixture;

    protected function setUp(): void
    {
        $this->platform = $this->createStub(AbstractPlatform::class);
        $this->fixture = new ConcreteTrickyLabelType();
    }

    #[Test]
    public function has_name(): void
    {
        $this->assertSame('test_tricky_label', $this->fixture->getName());
    }

    #[Test]
    public function returns_sql_declaration_as_type_name(): void
    {
        $this->assertSame('test_tricky_label', $this->fixture->getSQLDeclaration([], $this->platform));
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

    /**
     * TrickyLabels declares an empty label, but this type maps an empty database string to null, so that
     * one case cannot be read back through the scalar type. See EMPTY_LABEL in the round-trip provider.
     */
    #[Test]
    public function converts_empty_string_from_database_to_null(): void
    {
        $this->assertNull($this->fixture->convertToPHPValue('', $this->platform));
    }

    #[Test]
    public function converts_backed_enum_to_database_value(): void
    {
        $this->assertSame('plain', $this->fixture->convertToDatabaseValue(TrickyLabels::PLAIN, $this->platform));
    }

    #[Test]
    public function converts_database_string_to_backed_enum(): void
    {
        $this->assertSame(TrickyLabels::WITH_SPACE, $this->fixture->convertToPHPValue('with space', $this->platform));
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
            'string' => ['plain'],
            'array' => [['plain']],
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
            'array' => [['plain']],
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
}
