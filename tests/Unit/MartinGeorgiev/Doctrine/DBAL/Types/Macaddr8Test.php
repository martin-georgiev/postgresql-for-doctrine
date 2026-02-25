<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidMacaddr8ForDatabaseException;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidMacaddr8ForPHPException;
use MartinGeorgiev\Doctrine\DBAL\Types\Macaddr8;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class Macaddr8Test extends TestCase
{
    /**
     * @var AbstractPlatform&MockObject
     */
    private MockObject $platform;

    private Macaddr8 $fixture;

    protected function setUp(): void
    {
        $this->platform = $this->createMock(AbstractPlatform::class);
        $this->fixture = new Macaddr8();
    }

    #[Test]
    public function has_name(): void
    {
        $this->assertEquals('macaddr8', $this->fixture->getName());
    }

    #[DataProvider('provideValidTransformations')]
    #[Test]
    public function can_transform_from_php_value(?string $phpValue, ?string $postgresValue, ?string $platformValue): void
    {
        $this->assertEquals($postgresValue, $this->fixture->convertToDatabaseValue($phpValue, $this->platform));
    }

    #[DataProvider('provideValidTransformations')]
    #[Test]
    public function can_transform_to_php_value(?string $phpValue, ?string $postgresValue, ?string $platformValue): void
    {
        $this->assertEquals($platformValue, $this->fixture->convertToPHPValue($postgresValue, $this->platform));
    }

    /**
     * @return array<string, array{phpValue: string|null, postgresValue: string|null, platformValue: string|null}>
     */
    public static function provideValidTransformations(): array
    {
        return [
            'null' => [
                'phpValue' => null,
                'postgresValue' => null,
                'platformValue' => null,
            ],
            'colon-separated lowercase' => [
                'phpValue' => '08:00:2b:ff:fe:01:02:03',
                'postgresValue' => '08:00:2b:ff:fe:01:02:03',
                'platformValue' => '08:00:2b:ff:fe:01:02:03',
            ],
            'colon-separated uppercase' => [
                'phpValue' => '08:00:2B:FF:FE:01:02:03',
                'postgresValue' => '08:00:2b:ff:fe:01:02:03',
                'platformValue' => '08:00:2b:ff:fe:01:02:03',
            ],
            'hyphen-separated' => [
                'phpValue' => '08-00-2b-ff-fe-01-02-03',
                'postgresValue' => '08:00:2b:ff:fe:01:02:03',
                'platformValue' => '08:00:2b:ff:fe:01:02:03',
            ],
            'dot notation' => [
                'phpValue' => '0800.2bff.fe01.0203',
                'postgresValue' => '08:00:2b:ff:fe:01:02:03',
                'platformValue' => '08:00:2b:ff:fe:01:02:03',
            ],
            'no separator' => [
                'phpValue' => '08002BFFFE010203',
                'postgresValue' => '08:00:2b:ff:fe:01:02:03',
                'platformValue' => '08:00:2b:ff:fe:01:02:03',
            ],
        ];
    }

    #[DataProvider('provideInvalidDatabaseValueInputs')]
    #[Test]
    public function throws_exception_for_invalid_database_value_inputs(mixed $value): void
    {
        $this->expectException(InvalidMacaddr8ForPHPException::class);

        $this->fixture->convertToDatabaseValue($value, $this->platform);
    }

    /**
     * @return array<string, array{mixed}>
     */
    public static function provideInvalidDatabaseValueInputs(): array
    {
        return [
            'empty string' => [''],
            'too short (6-octet macaddr)' => ['08:00:2b:01:02:03'],
            'too long (9 octets)' => ['08:00:2b:ff:fe:01:02:03:04'],
            'invalid hex chars' => ['08:00:2b:zz:fe:01:02:03'],
            'mixed separators' => ['08:00-2b:ff:fe:01:02:03'],
            'integer input' => [123],
            'array input' => [['not', 'string']],
            'boolean input' => [false],
            'float input' => [3.14],
            'object input' => [new \stdClass()],
        ];
    }

    #[DataProvider('provideInvalidPHPValueInputs')]
    #[Test]
    public function throws_exception_for_invalid_php_value_inputs(mixed $value): void
    {
        $this->expectException(InvalidMacaddr8ForDatabaseException::class);

        $this->fixture->convertToPHPValue($value, $this->platform);
    }

    /**
     * @return array<string, array{mixed}>
     */
    public static function provideInvalidPHPValueInputs(): array
    {
        return [
            'integer input' => [123],
            'array input' => [['not', 'string']],
            'boolean input' => [true],
            'float input' => [3.14],
            'object input' => [new \stdClass()],
        ];
    }

    #[DataProvider('provideInvalidPHPValueFormats')]
    #[Test]
    public function throws_exception_for_invalid_php_value_formats(string $value): void
    {
        $this->expectException(InvalidMacaddr8ForDatabaseException::class);
        $this->expectExceptionMessage('Invalid EUI-64 MAC address format in database');

        $this->fixture->convertToPHPValue($value, $this->platform);
    }

    /**
     * @return array<string, array{string}>
     */
    public static function provideInvalidPHPValueFormats(): array
    {
        return [
            'too short' => ['08:00:2b:01:02:03'],
            'invalid format' => ['not-a-mac-address'],
            'empty string' => [''],
        ];
    }
}
