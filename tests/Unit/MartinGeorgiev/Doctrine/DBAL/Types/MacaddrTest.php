<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidMacaddrForDatabaseException;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidMacaddrForPHPException;
use MartinGeorgiev\Doctrine\DBAL\Types\Macaddr;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class MacaddrTest extends TestCase
{
    /**
     * @var AbstractPlatform&MockObject
     */
    private MockObject $platform;

    private Macaddr $fixture;

    protected function setUp(): void
    {
        $this->platform = $this->createMock(AbstractPlatform::class);
        $this->fixture = new Macaddr();
    }

    #[Test]
    public function has_name(): void
    {
        $this->assertEquals('macaddr', $this->fixture->getName());
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
                'phpValue' => '08:00:2b:01:02:03',
                'postgresValue' => '08:00:2b:01:02:03',
                'platformValue' => '08:00:2b:01:02:03',
            ],
            'colon-separated uppercase' => [
                'phpValue' => '08:00:2B:01:02:03',
                'postgresValue' => '08:00:2b:01:02:03',
                'platformValue' => '08:00:2b:01:02:03',
            ],
            'hyphen-separated lowercase' => [
                'phpValue' => '08-00-2b-01-02-03',
                'postgresValue' => '08:00:2b:01:02:03',
                'platformValue' => '08:00:2b:01:02:03',
            ],
            'hyphen-separated uppercase' => [
                'phpValue' => '08-00-2B-01-02-03',
                'postgresValue' => '08:00:2b:01:02:03',
                'platformValue' => '08:00:2b:01:02:03',
            ],
            'no separator' => [
                'phpValue' => '08002B010203',
                'postgresValue' => '08:00:2b:01:02:03',
                'platformValue' => '08:00:2b:01:02:03',
            ],
            'mixed case no separator' => [
                'phpValue' => '08002b010203',
                'postgresValue' => '08:00:2b:01:02:03',
                'platformValue' => '08:00:2b:01:02:03',
            ],
        ];
    }

    #[DataProvider('provideInvalidDatabaseValueInputs')]
    #[Test]
    public function throws_exception_for_invalid_database_value_inputs(mixed $phpValue): void
    {
        $this->expectException(InvalidMacaddrForPHPException::class);

        $this->fixture->convertToDatabaseValue($phpValue, $this->platform);
    }

    /**
     * @return array<string, array{mixed}>
     */
    public static function provideInvalidDatabaseValueInputs(): array
    {
        return [
            // Invalid MAC address formats
            'empty string' => [''],
            'invalid format' => ['00:11:22:33:44'],
            'invalid characters' => ['00:11:22:gg:hh:ii'],
            'too short' => ['00:11:22:33:44'],
            'too long' => ['00:11:22:33:44:55:66'],
            'invalid separator' => ['00.11.22.33.44.55'],
            'non-hex characters' => ['GG:HH:II:JJ:KK:LL'],
            'wrong format' => ['not-a-mac-address'],
            'mixed separators' => ['00:11-22:33-44:55'],
            'non-hex digits' => ['zz:zz:zz:zz:zz:zz'],
            // Invalid types
            'integer input' => [123],
            'array input' => [['not', 'string']],
            'boolean input' => [false],
            'object input' => [new \stdClass()],
            'float input' => [3.14],
        ];
    }

    #[DataProvider('provideInvalidPHPValueInputs')]
    #[Test]
    public function throws_exception_for_invalid_php_value_inputs(mixed $invalidValue): void
    {
        $this->expectException(InvalidMacaddrForDatabaseException::class);
        $this->expectExceptionMessage('Database value must be a string');

        $this->fixture->convertToPHPValue($invalidValue, $this->platform);
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
            'object input' => [new \stdClass()],
            'float input' => [3.14],
        ];
    }

    #[DataProvider('provideInvalidPHPValueFormats')]
    #[Test]
    public function throws_exception_for_invalid_php_value_formats(string $invalidFormat): void
    {
        $this->expectException(InvalidMacaddrForDatabaseException::class);
        $this->expectExceptionMessage('Invalid MAC address format in database');

        $this->fixture->convertToPHPValue($invalidFormat, $this->platform);
    }

    /**
     * @return array<string, array{string}>
     */
    public static function provideInvalidPHPValueFormats(): array
    {
        return [
            'invalid format from database' => ['invalid-mac-format'],
            'empty string' => [''],
            'malformed mac' => ['00:11:22:33:44'],
        ];
    }
}
