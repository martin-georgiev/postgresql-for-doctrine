<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidMacaddrForDatabaseException;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidMacaddrForPHPException;
use MartinGeorgiev\Doctrine\DBAL\Types\Macaddr;
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

    /**
     * @test
     */
    public function has_name(): void
    {
        self::assertEquals('macaddr', $this->fixture->getName());
    }

    /**
     * @test
     *
     * @dataProvider provideValidTransformations
     */
    public function can_transform_from_php_value(?string $phpInput, ?string $postgresValueAfterNormalisation, ?string $phpValueAfterRetrievalFromDatabase): void
    {
        self::assertEquals($postgresValueAfterNormalisation, $this->fixture->convertToDatabaseValue($phpInput, $this->platform));
    }

    /**
     * @test
     *
     * @dataProvider provideValidTransformations
     */
    public function can_transform_to_php_value(?string $phpInput, ?string $postgresValueAfterNormalisation, ?string $phpValueAfterRetrievalFromDatabase): void
    {
        self::assertEquals($phpValueAfterRetrievalFromDatabase, $this->fixture->convertToPHPValue($postgresValueAfterNormalisation, $this->platform));
    }

    /**
     * @return array<string, array{phpInput: string|null, postgresValueAfterNormalisation: string|null, postgresValueAfterNormalisation: string|null}>
     */
    public static function provideValidTransformations(): array
    {
        return [
            'null' => [
                'phpInput' => null,
                'postgresValueAfterNormalisation' => null,
                'phpValueAfterRetrievalFromDatabase' => null,
            ],
            'colon-separated lowercase' => [
                'phpInput' => '08:00:2b:01:02:03',
                'postgresValueAfterNormalisation' => '08:00:2b:01:02:03',
                'phpValueAfterRetrievalFromDatabase' => '08:00:2b:01:02:03',
            ],
            'colon-separated uppercase' => [
                'phpInput' => '08:00:2B:01:02:03',
                'postgresValueAfterNormalisation' => '08:00:2b:01:02:03',
                'phpValueAfterRetrievalFromDatabase' => '08:00:2b:01:02:03',
            ],
            'hyphen-separated lowercase' => [
                'phpInput' => '08-00-2b-01-02-03',
                'postgresValueAfterNormalisation' => '08:00:2b:01:02:03',
                'phpValueAfterRetrievalFromDatabase' => '08:00:2b:01:02:03',
            ],
            'hyphen-separated uppercase' => [
                'phpInput' => '08-00-2B-01-02-03',
                'postgresValueAfterNormalisation' => '08:00:2b:01:02:03',
                'phpValueAfterRetrievalFromDatabase' => '08:00:2b:01:02:03',
            ],
            'no separator' => [
                'phpInput' => '08002B010203',
                'postgresValueAfterNormalisation' => '08:00:2b:01:02:03',
                'phpValueAfterRetrievalFromDatabase' => '08:00:2b:01:02:03',
            ],
            'mixed case no separator' => [
                'phpInput' => '08002b010203',
                'postgresValueAfterNormalisation' => '08:00:2b:01:02:03',
                'phpValueAfterRetrievalFromDatabase' => '08:00:2b:01:02:03',
            ],
        ];
    }

    /**
     * @test
     *
     * @dataProvider provideInvalidPHPValuesForDatabaseTransformation
     */
    public function throws_exception_when_invalid_data_provided_to_convert_to_database_value(mixed $phpValue): void
    {
        $this->expectException(InvalidMacaddrForPHPException::class);
        $this->fixture->convertToDatabaseValue($phpValue, $this->platform);
    }

    /**
     * @return array<string, array{mixed}>
     */
    public static function provideInvalidPHPValuesForDatabaseTransformation(): array
    {
        return [
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
        ];
    }

    /**
     * @test
     *
     * @dataProvider provideInvalidDatabaseValuesForPHPTransformation
     */
    public function throws_exception_when_invalid_data_provided_to_convert_to_php_value(mixed $dbValue): void
    {
        $this->expectException(InvalidMacaddrForDatabaseException::class);
        $this->fixture->convertToPHPValue($dbValue, $this->platform);
    }

    /**
     * @return array<string, array{mixed}>
     */
    public static function provideInvalidDatabaseValuesForPHPTransformation(): array
    {
        return [
            'invalid type' => [123],
            'invalid format from database' => ['invalid-mac-format'],
        ];
    }
}
