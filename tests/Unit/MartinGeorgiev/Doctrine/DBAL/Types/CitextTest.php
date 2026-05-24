<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use MartinGeorgiev\Doctrine\DBAL\Types\Citext;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidCitextForDatabaseException;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidCitextForPHPException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class CitextTest extends TestCase
{
    /**
     * @var AbstractPlatform&MockObject
     */
    private MockObject $platform;

    private Citext $fixture;

    protected function setUp(): void
    {
        $this->platform = $this->createMock(AbstractPlatform::class);
        $this->fixture = new Citext();
    }

    #[Test]
    public function has_name(): void
    {
        $this->assertSame('citext', $this->fixture->getName());
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

    #[DataProvider('provideValidStringValues')]
    #[Test]
    public function can_convert_string_to_database_value(string $value): void
    {
        $this->assertSame($value, $this->fixture->convertToDatabaseValue($value, $this->platform));
    }

    #[DataProvider('provideValidStringValues')]
    #[Test]
    public function can_convert_string_to_php_value(string $value): void
    {
        $this->assertSame($value, $this->fixture->convertToPHPValue($value, $this->platform));
    }

    /**
     * @return array<string, array{string}>
     */
    public static function provideValidStringValues(): array
    {
        return [
            'simple text' => ['hello'],
            'mixed case' => ['Hello World'],
            'email' => ['User@Example.COM'],
        ];
    }

    #[DataProvider('provideInvalidDatabaseValueInputs')]
    #[Test]
    public function throws_exception_for_invalid_database_value(mixed $value): void
    {
        $this->expectException(InvalidCitextForDatabaseException::class);

        $this->fixture->convertToDatabaseValue($value, $this->platform);
    }

    /**
     * @return array<string, array{mixed}>
     */
    public static function provideInvalidDatabaseValueInputs(): array
    {
        return [
            'integer input' => [42],
            'array input' => [['not', 'a', 'string']],
            'object input' => [new \stdClass()],
        ];
    }

    #[DataProvider('provideInvalidPHPValueInputs')]
    #[Test]
    public function throws_exception_for_invalid_php_value(mixed $value): void
    {
        $this->expectException(InvalidCitextForPHPException::class);

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
            'object input' => [new \stdClass()],
        ];
    }
}
