<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidMultirangeForDatabaseException;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidMultirangeForPHPException;
use MartinGeorgiev\Doctrine\DBAL\Types\TstzMultirange;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\TstzMultirange as TstzMultirangeVO;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\TstzRange;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class TstzMultirangeTest extends TestCase
{
    /**
     * @var AbstractPlatform&MockObject
     */
    private MockObject $platform;

    private TstzMultirange $fixture;

    protected function setUp(): void
    {
        $this->platform = $this->createMock(AbstractPlatform::class);
        $this->fixture = new TstzMultirange();
    }

    #[Test]
    public function has_name(): void
    {
        $this->assertSame('tstzmultirange', $this->fixture->getName());
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
    public function can_convert_to_database_value(TstzMultirangeVO $tstzMultirangeVO, string $expectedString): void
    {
        $this->assertSame($expectedString, $this->fixture->convertToDatabaseValue($tstzMultirangeVO, $this->platform));
    }

    /**
     * @return array<string, array{TstzMultirangeVO, string}>
     */
    public static function provideValidDatabaseConversions(): array
    {
        return [
            'empty multirange' => [new TstzMultirangeVO([]), '{}'],
            'single range' => [
                new TstzMultirangeVO([new TstzRange(new \DateTimeImmutable('2024-01-01 09:00:00+00:00'), new \DateTimeImmutable('2024-01-01 17:00:00+00:00'))]),
                '{[2024-01-01 09:00:00.000000+00:00,2024-01-01 17:00:00.000000+00:00)}',
            ],
            'two ranges' => [
                new TstzMultirangeVO([
                    new TstzRange(new \DateTimeImmutable('2024-01-01 09:00:00+00:00'), new \DateTimeImmutable('2024-01-01 12:00:00+00:00')),
                    new TstzRange(new \DateTimeImmutable('2024-01-01 14:00:00+00:00'), new \DateTimeImmutable('2024-01-01 17:00:00+00:00')),
                ]),
                '{[2024-01-01 09:00:00.000000+00:00,2024-01-01 12:00:00.000000+00:00),[2024-01-01 14:00:00.000000+00:00,2024-01-01 17:00:00.000000+00:00)}',
            ],
        ];
    }

    #[DataProvider('provideValidPHPConversions')]
    #[Test]
    public function can_convert_to_php_value(string $input, string $expectedString): void
    {
        $result = $this->fixture->convertToPHPValue($input, $this->platform);
        $this->assertInstanceOf(TstzMultirangeVO::class, $result);
        $this->assertSame($expectedString, (string) $result);
    }

    /**
     * @return array<string, array{string, string}>
     */
    public static function provideValidPHPConversions(): array
    {
        return [
            'empty multirange' => ['{}', '{}'],
            'single range' => ['{[2024-01-01 09:00:00+00:00,2024-01-01 17:00:00+00:00)}', '{[2024-01-01 09:00:00.000000+00:00,2024-01-01 17:00:00.000000+00:00)}'],
            'two ranges' => [
                '{[2024-01-01 09:00:00+00:00,2024-01-01 12:00:00+00:00),[2024-01-01 14:00:00+00:00,2024-01-01 17:00:00+00:00)}',
                '{[2024-01-01 09:00:00.000000+00:00,2024-01-01 12:00:00.000000+00:00),[2024-01-01 14:00:00.000000+00:00,2024-01-01 17:00:00.000000+00:00)}',
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

    #[Test]
    public function throws_exception_for_invalid_php_value_type(): void
    {
        $this->expectException(InvalidMultirangeForPHPException::class);

        $this->fixture->convertToPHPValue(123, $this->platform); // @phpstan-ignore-line
    }

    #[Test]
    public function throws_exception_for_invalid_php_value_format(): void
    {
        $this->expectException(InvalidMultirangeForPHPException::class);

        $this->fixture->convertToPHPValue('invalid-multirange-format', $this->platform);
    }
}
