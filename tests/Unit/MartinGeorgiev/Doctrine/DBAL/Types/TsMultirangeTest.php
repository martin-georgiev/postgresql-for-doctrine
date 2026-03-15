<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidMultirangeForDatabaseException;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidMultirangeForPHPException;
use MartinGeorgiev\Doctrine\DBAL\Types\TsMultirange;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\TsMultirange as TsMultirangeVO;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\TsRange;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class TsMultirangeTest extends TestCase
{
    /**
     * @var AbstractPlatform&MockObject
     */
    private MockObject $platform;

    private TsMultirange $fixture;

    protected function setUp(): void
    {
        $this->platform = $this->createMock(AbstractPlatform::class);
        $this->fixture = new TsMultirange();
    }

    #[Test]
    public function has_name(): void
    {
        $this->assertSame('tsmultirange', $this->fixture->getName());
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

    #[DataProvider('provideValidDatabaseConversions')]
    #[Test]
    public function can_convert_to_database_value(TsMultirangeVO $tsMultirangeVO, string $expectedString): void
    {
        $this->assertSame($expectedString, $this->fixture->convertToDatabaseValue($tsMultirangeVO, $this->platform));
    }

    /**
     * @return array<string, array{TsMultirangeVO, string}>
     */
    public static function provideValidDatabaseConversions(): array
    {
        return [
            'empty multirange' => [new TsMultirangeVO([]), '{}'],
            'single range' => [
                new TsMultirangeVO([new TsRange(new \DateTimeImmutable('2024-01-01 09:00:00'), new \DateTimeImmutable('2024-01-01 17:00:00'))]),
                '{[2024-01-01 09:00:00.000000,2024-01-01 17:00:00.000000)}',
            ],
            'two ranges' => [
                new TsMultirangeVO([
                    new TsRange(new \DateTimeImmutable('2024-01-01 09:00:00'), new \DateTimeImmutable('2024-01-01 12:00:00')),
                    new TsRange(new \DateTimeImmutable('2024-01-01 14:00:00'), new \DateTimeImmutable('2024-01-01 17:00:00')),
                ]),
                '{[2024-01-01 09:00:00.000000,2024-01-01 12:00:00.000000),[2024-01-01 14:00:00.000000,2024-01-01 17:00:00.000000)}',
            ],
        ];
    }

    #[DataProvider('provideValidPHPConversions')]
    #[Test]
    public function can_convert_to_php_value(string $input, string $expectedString): void
    {
        $result = $this->fixture->convertToPHPValue($input, $this->platform);
        $this->assertInstanceOf(TsMultirangeVO::class, $result);
        $this->assertSame($expectedString, (string) $result);
    }

    /**
     * @return array<string, array{string, string}>
     */
    public static function provideValidPHPConversions(): array
    {
        return [
            'empty multirange' => ['{}', '{}'],
            'single range' => ['{[2024-01-01 09:00:00,2024-01-01 17:00:00)}', '{[2024-01-01 09:00:00.000000,2024-01-01 17:00:00.000000)}'],
            'two ranges' => [
                '{[2024-01-01 09:00:00,2024-01-01 12:00:00),[2024-01-01 14:00:00,2024-01-01 17:00:00)}',
                '{[2024-01-01 09:00:00.000000,2024-01-01 12:00:00.000000),[2024-01-01 14:00:00.000000,2024-01-01 17:00:00.000000)}',
            ],
        ];
    }

    #[Test]
    public function converts_empty_string_from_database_to_null(): void
    {
        $this->assertNull($this->fixture->convertToPHPValue('', $this->platform));
    }

    #[Test]
    public function throws_exception_for_invalid_database_value_type(): void
    {
        $this->expectException(InvalidMultirangeForDatabaseException::class);

        $this->fixture->convertToDatabaseValue('not-a-multirange', $this->platform); // @phpstan-ignore-line
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
}
