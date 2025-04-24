<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use MartinGeorgiev\Doctrine\DBAL\Types\Jsonb;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class JsonbTest extends TestCase
{
    /**
     * @var AbstractPlatform&MockObject
     */
    private MockObject $platform;

    private Jsonb $fixture;

    protected function setUp(): void
    {
        $this->platform = $this->createMock(AbstractPlatform::class);

        $this->fixture = new Jsonb();
    }

    /**
     * @test
     */
    public function has_name(): void
    {
        self::assertEquals('jsonb', $this->fixture->getName());
    }

    /**
     * @test
     *
     * @dataProvider provideValidTransformations
     */
    public function can_transform_from_php_value(null|array|bool|float|int|string $phpValue, ?string $postgresValue): void
    {
        self::assertEquals($postgresValue, $this->fixture->convertToDatabaseValue($phpValue, $this->platform));
    }

    /**
     * @test
     *
     * @dataProvider provideValidTransformations
     */
    public function can_transform_to_php_value(null|array|bool|float|int|string $phpValue, ?string $postgresValue): void
    {
        self::assertEquals($phpValue, $this->fixture->convertToPHPValue($postgresValue, $this->platform));
    }

    /**
     * @return list<array{
     *     phpValue: null|array|bool|float|int|string,
     *     postgresValue: string|null
     * }>
     */
    public static function provideValidTransformations(): array
    {
        return [
            [
                'phpValue' => null,
                'postgresValue' => null,
            ],
            [
                'phpValue' => true,
                'postgresValue' => 'true',
            ],
            [
                'phpValue' => [],
                'postgresValue' => '[]',
            ],
            [
                'phpValue' => 13,
                'postgresValue' => '13',
            ],
            [
                'phpValue' => 13.93,
                'postgresValue' => '13.93',
            ],
            [
                'phpValue' => 'a string value',
                'postgresValue' => '"a string value"',
            ],
            [
                'phpValue' => [681, 1185, 1878, 1989],
                'postgresValue' => '[681,1185,1878,1989]',
            ],
            [
                'phpValue' => [
                    'key1' => 'value1',
                    'key2' => false,
                    'key3' => '15',
                    'key4' => 15,
                    'key5' => [112, 242, 309, 310],
                ],
                'postgresValue' => '{"key1":"value1","key2":false,"key3":"15","key4":15,"key5":[112,242,309,310]}',
            ],
        ];
    }
}
