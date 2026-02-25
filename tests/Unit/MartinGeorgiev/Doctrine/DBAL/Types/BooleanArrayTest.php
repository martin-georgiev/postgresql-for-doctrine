<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use MartinGeorgiev\Doctrine\DBAL\Types\BooleanArray;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class BooleanArrayTest extends TestCase
{
    /**
     * @var AbstractPlatform&MockObject
     */
    private MockObject $platform;

    private BooleanArray $fixture;

    protected function setUp(): void
    {
        $this->platform = $this->createMock(AbstractPlatform::class);

        $this->fixture = new BooleanArray();
    }

    #[Test]
    public function has_name(): void
    {
        $this->assertSame('bool[]', $this->fixture->getName());
    }

    #[DataProvider('provideValidTransformations')]
    #[Test]
    public function can_transform_from_php_value(?array $phpValue, ?string $postgresValue, ?array $platformValue): void
    {
        $this->platform->method('convertBooleansToDatabaseValue')
            ->with($phpValue)
            ->willReturn($platformValue);

        $this->assertSame($postgresValue, $this->fixture->convertToDatabaseValue($phpValue, $this->platform));
    }

    #[DataProvider('provideValidTransformations')]
    #[Test]
    public function can_transform_to_php_value(?array $phpValue, ?string $postgresValue, ?array $platformValue = null): void
    {
        $this->platform->method('convertFromBoolean')
            ->with($this->anything())
            ->willReturnCallback('boolval');

        $this->assertSame($phpValue, $this->fixture->convertToPHPValue($postgresValue, $this->platform));
    }

    /**
     * @return list<array{
     *     phpValue: array|null,
     *     postgresValue: string|null,
     *     platformValue: array|null
     * }>
     */
    public static function provideValidTransformations(): array
    {
        return [
            [
                'phpValue' => null,
                'postgresValue' => null,
                'platformValue' => null,
            ],
            [
                'phpValue' => [],
                'postgresValue' => '{}',
                'platformValue' => [],
            ],
            [
                'phpValue' => [true, false, true],
                'postgresValue' => '{1,0,1}',
                'platformValue' => ['1', '0', '1'],
            ],
        ];
    }
}
