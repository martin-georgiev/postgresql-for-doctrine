<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidMultirangeForDatabaseException;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidMultirangeForPHPException;
use MartinGeorgiev\Doctrine\DBAL\Types\Int4Multirange;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Int4Multirange as Int4MultirangeVO;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Int4Range;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class Int4MultirangeTest extends TestCase
{
    /**
     * @var AbstractPlatform&MockObject
     */
    private MockObject $platform;

    private Int4Multirange $fixture;

    protected function setUp(): void
    {
        $this->platform = $this->createMock(AbstractPlatform::class);
        $this->fixture = new Int4Multirange();
    }

    #[Test]
    public function has_name(): void
    {
        $this->assertEquals('int4multirange', $this->fixture->getName());
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
    public function can_convert_to_database_value(Int4MultirangeVO $int4MultirangeVO, string $expectedString): void
    {
        $this->assertSame($expectedString, $this->fixture->convertToDatabaseValue($int4MultirangeVO, $this->platform));
    }

    /**
     * @return array<string, array{Int4MultirangeVO, string}>
     */
    public static function provideValidDatabaseConversions(): array
    {
        return [
            'empty multirange' => [new Int4MultirangeVO([]), '{}'],
            'single range' => [new Int4MultirangeVO([new Int4Range(1, 10)]), '{[1,10)}'],
            'two ranges' => [
                new Int4MultirangeVO([new Int4Range(1, 5), new Int4Range(10, 20)]),
                '{[1,5),[10,20)}',
            ],
        ];
    }

    #[DataProvider('provideValidPHPConversions')]
    #[Test]
    public function can_convert_to_php_value(string $input, string $expectedString): void
    {
        $result = $this->fixture->convertToPHPValue($input, $this->platform);
        $this->assertInstanceOf(Int4MultirangeVO::class, $result);
        $this->assertSame($expectedString, (string) $result);
    }

    /**
     * @return array<string, array{string, string}>
     */
    public static function provideValidPHPConversions(): array
    {
        return [
            'empty multirange' => ['{}', '{}'],
            'single range' => ['{[1,10)}', '{[1,10)}'],
            'two ranges' => ['{[1,5),[10,20)}', '{[1,5),[10,20)}'],
        ];
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
