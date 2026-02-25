<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidMultirangeForDatabaseException;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidMultirangeForPHPException;
use MartinGeorgiev\Doctrine\DBAL\Types\NumMultirange;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\NumericMultirange as NumericMultirangeVO;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\NumericRange;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class NumMultirangeTest extends TestCase
{
    /**
     * @var AbstractPlatform&MockObject
     */
    private MockObject $platform;

    private NumMultirange $fixture;

    protected function setUp(): void
    {
        $this->platform = $this->createMock(AbstractPlatform::class);
        $this->fixture = new NumMultirange();
    }

    #[Test]
    public function has_name(): void
    {
        $this->assertSame('nummultirange', $this->fixture->getName());
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
    public function can_convert_to_database_value(NumericMultirangeVO $numericMultirangeVO, string $expectedString): void
    {
        $this->assertSame($expectedString, $this->fixture->convertToDatabaseValue($numericMultirangeVO, $this->platform));
    }

    /**
     * @return array<string, array{NumericMultirangeVO, string}>
     */
    public static function provideValidDatabaseConversions(): array
    {
        return [
            'empty multirange' => [new NumericMultirangeVO([]), '{}'],
            'single decimal range' => [new NumericMultirangeVO([new NumericRange(1.5, 10.5)]), '{[1.5,10.5)}'],
            'two ranges' => [
                new NumericMultirangeVO([new NumericRange(1, 5), new NumericRange(10.5, 20.5)]),
                '{[1,5),[10.5,20.5)}',
            ],
        ];
    }

    #[DataProvider('provideValidPHPConversions')]
    #[Test]
    public function can_convert_to_php_value(string $input, string $expectedString): void
    {
        $result = $this->fixture->convertToPHPValue($input, $this->platform);
        $this->assertInstanceOf(NumericMultirangeVO::class, $result);
        $this->assertSame($expectedString, (string) $result);
    }

    /**
     * @return array<string, array{string, string}>
     */
    public static function provideValidPHPConversions(): array
    {
        return [
            'empty multirange' => ['{}', '{}'],
            'single decimal range' => ['{[1.5,10.5)}', '{[1.5,10.5)}'],
            'two ranges' => ['{[1,5),[10.5,20.5)}', '{[1,5),[10.5,20.5)}'],
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
