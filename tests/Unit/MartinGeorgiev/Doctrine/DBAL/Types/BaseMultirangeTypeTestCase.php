<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use MartinGeorgiev\Doctrine\DBAL\Types\BaseMultirangeType;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidMultirangeForDatabaseException;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidMultirangeForPHPException;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Multirange;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @template M of Multirange
 */
abstract class BaseMultirangeTypeTestCase extends TestCase
{
    /**
     * @var AbstractPlatform&MockObject
     */
    protected MockObject $platform;

    /**
     * @var BaseMultirangeType<M>
     */
    protected BaseMultirangeType $fixture;

    protected function setUp(): void
    {
        $this->platform = $this->createMock(AbstractPlatform::class);
        $this->fixture = $this->createMultirangeType();
    }

    #[Test]
    public function has_name(): void
    {
        $this->assertSame($this->getExpectedTypeName(), $this->fixture->getName());
    }

    #[Test]
    public function converts_null_to_database(): void
    {
        $this->assertNull($this->fixture->convertToDatabaseValue(null, $this->platform));
    }

    #[Test]
    public function converts_null_from_database(): void
    {
        $this->assertNull($this->fixture->convertToPHPValue(null, $this->platform));
    }

    /**
     * @param M $multirange
     */
    #[DataProvider('provideValidDatabaseConversions')]
    #[Test]
    public function can_convert_to_database_value(Multirange $multirange, string $expectedString): void
    {
        $this->assertSame($expectedString, $this->fixture->convertToDatabaseValue($multirange, $this->platform));
    }

    #[DataProvider('provideValidPHPConversions')]
    #[Test]
    public function can_convert_to_php_value(string $input, string $expectedString): void
    {
        $result = $this->fixture->convertToPHPValue($input, $this->platform);
        $this->assertInstanceOf($this->getExpectedValueObjectClass(), $result);
        $this->assertSame($expectedString, (string) $result);
    }

    #[Test]
    public function converts_empty_string_from_database_to_null(): void
    {
        $this->assertNull($this->fixture->convertToPHPValue('', $this->platform));
    }

    #[DataProvider('provideInvalidDatabaseValueInputs')]
    #[Test]
    public function throws_exception_for_invalid_database_value_type(mixed $input): void
    {
        $this->expectException(InvalidMultirangeForDatabaseException::class);

        $this->fixture->convertToDatabaseValue($input, $this->platform); // @phpstan-ignore argument.type
    }

    /**
     * @return array<string, array{mixed}>
     */
    public static function provideInvalidDatabaseValueInputs(): array
    {
        return [
            'string' => ['not-a-multirange'],
            'integer' => [123],
            'array' => [[]],
        ];
    }

    #[DataProvider('provideInvalidPHPValueInputs')]
    #[Test]
    public function throws_exception_for_invalid_php_value_type(mixed $input): void
    {
        $this->expectException(InvalidMultirangeForPHPException::class);

        $this->fixture->convertToPHPValue($input, $this->platform); // @phpstan-ignore argument.type
    }

    /**
     * @return array<string, array{mixed}>
     */
    public static function provideInvalidPHPValueInputs(): array
    {
        return [
            'integer' => [123],
            'float' => [1.5],
            'array' => [[]],
            'object' => [new \stdClass()],
            'bool' => [true],
        ];
    }

    #[Test]
    public function throws_exception_for_invalid_php_value_format(): void
    {
        $this->expectException(InvalidMultirangeForPHPException::class);

        $this->fixture->convertToPHPValue('invalid-multirange-format', $this->platform);
    }

    /**
     * @return BaseMultirangeType<M>
     */
    abstract protected function createMultirangeType(): BaseMultirangeType;

    abstract protected function getExpectedTypeName(): string;

    /**
     * @return class-string<M>
     */
    abstract protected function getExpectedValueObjectClass(): string;
}
