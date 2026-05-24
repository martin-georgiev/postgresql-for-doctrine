<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Fixtures\MartinGeorgiev\Doctrine\Colors;
use Fixtures\MartinGeorgiev\Doctrine\ConcreteColorType;
use Fixtures\MartinGeorgiev\Doctrine\Sizes;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidEnumForDatabaseException;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidEnumForPHPException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class EnumTest extends TestCase
{
    /**
     * @var AbstractPlatform&MockObject
     */
    private MockObject $platform;

    private ConcreteColorType $fixture;

    protected function setUp(): void
    {
        $this->platform = $this->createMock(AbstractPlatform::class);
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
    public function throws_for_non_backed_enum_in_database_value(mixed $value): void
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
    public function throws_for_wrong_enum_class_in_database_value(): void
    {
        $this->expectException(InvalidEnumForDatabaseException::class);

        $this->fixture->convertToDatabaseValue(Sizes::SMALL, $this->platform);
    }

    #[DataProvider('provideNonStringPhpValues')]
    #[Test]
    public function throws_for_non_string_in_php_value(mixed $value): void
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
    public function throws_for_unknown_enum_value_in_php_value(): void
    {
        $this->expectException(InvalidEnumForPHPException::class);

        $this->fixture->convertToPHPValue('purple', $this->platform);
    }
}
