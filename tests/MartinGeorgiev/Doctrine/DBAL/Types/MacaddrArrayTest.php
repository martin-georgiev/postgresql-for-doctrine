<?php

declare(strict_types=1);

namespace Tests\MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidMacaddrArrayItemForPHPException;
use MartinGeorgiev\Doctrine\DBAL\Types\MacaddrArray;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class MacaddrArrayTest extends TestCase
{
    /**
     * @var AbstractPlatform&MockObject
     */
    private AbstractPlatform $platform;

    private MacaddrArray $fixture;

    protected function setUp(): void
    {
        $this->platform = $this->createMock(AbstractPlatform::class);
        $this->fixture = new MacaddrArray();
    }

    /**
     * @test
     */
    public function has_name(): void
    {
        self::assertEquals('macaddr[]', $this->fixture->getName());
    }

    /**
     * @test
     *
     * @dataProvider provideValidTransformations
     */
    public function can_transform_from_php_value(?array $phpValue, ?string $postgresValue): void
    {
        self::assertEquals($postgresValue, $this->fixture->convertToDatabaseValue($phpValue, $this->platform));
    }

    /**
     * @test
     *
     * @dataProvider provideValidTransformations
     */
    public function can_transform_to_php_value(?array $phpValue, ?string $postgresValue): void
    {
        self::assertEquals($phpValue, $this->fixture->convertToPHPValue($postgresValue, $this->platform));
    }

    /**
     * @return array<string, array{phpValue: array|null, postgresValue: string|null}>
     */
    public static function provideValidTransformations(): array
    {
        return [
            'null' => [
                'phpValue' => null,
                'postgresValue' => null,
            ],
            'empty array' => [
                'phpValue' => [],
                'postgresValue' => '{}',
            ],
            'colon-separated MAC addresses' => [
                'phpValue' => ['08:00:2b:01:02:03', '00:0c:29:aa:bb:cc'],
                'postgresValue' => '{"08:00:2b:01:02:03","00:0c:29:aa:bb:cc"}',
            ],
            'hyphen-separated MAC addresses' => [
                'phpValue' => ['08-00-2b-01-02-03', '00-0c-29-aa-bb-cc'],
                'postgresValue' => '{"08-00-2b-01-02-03","00-0c-29-aa-bb-cc"}',
            ],
            'mixed separator MAC addresses' => [
                'phpValue' => ['08:00:2b:01:02:03', '00-0c-29-aa-bb-cc'],
                'postgresValue' => '{"08:00:2b:01:02:03","00-0c-29-aa-bb-cc"}',
            ],
        ];
    }

    /**
     * @test
     *
     * @dataProvider provideInvalidTransformations
     */
    public function throws_exception_when_invalid_data_provided_to_convert_to_database_value(mixed $phpValue): void
    {
        $this->expectException(InvalidMacaddrArrayItemForPHPException::class);
        $this->fixture->convertToDatabaseValue($phpValue, $this->platform); // @phpstan-ignore-line
    }

    /**
     * @return array<string, mixed>
     */
    public static function provideInvalidTransformations(): array
    {
        return [
            'invalid type' => ['not-an-array'],
            'invalid MAC format' => [['00:11:22:33:44:ZZ']],
            'too short' => [['00:11:22:33:44']],
            'too long' => [['00:11:22:33:44:55:66']],
            'no separators' => [['000011223344']],
            'wrong separator' => [['00.11.22.33.44.55']],
            'non-hex characters' => [['GG:HH:II:JJ:KK:LL']],
        ];
    }
}
