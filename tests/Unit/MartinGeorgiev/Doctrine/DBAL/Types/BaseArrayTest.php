<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use MartinGeorgiev\Doctrine\DBAL\Types\BaseArray;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class BaseArrayTest extends TestCase
{
    /**
     * @var AbstractPlatform&MockObject
     */
    private MockObject $platform;

    /**
     * @var BaseArray&MockObject
     */
    private MockObject $fixture;

    protected function setUp(): void
    {
        $this->platform = $this->createMock(AbstractPlatform::class);

        $this->fixture = $this->getMockBuilder(BaseArray::class)
            ->onlyMethods(['isValidArrayItemForDatabase'])
            ->disableOriginalConstructor()
            ->getMock();
    }

    #[DataProvider('provideValidTransformations')]
    #[Test]
    public function can_transform_from_php_value(?array $phpValue, ?string $postgresValue): void
    {
        $this->fixture
            ->method('isValidArrayItemForDatabase')
            ->willReturn(true);

        $this->assertSame($postgresValue, $this->fixture->convertToDatabaseValue($phpValue, $this->platform));
    }

    #[DataProvider('provideValidTransformations')]
    #[Test]
    public function can_transform_to_php_value(?array $phpValue, ?string $postgresValue): void
    {
        $this->assertEquals($phpValue, $this->fixture->convertToPHPValue($postgresValue, $this->platform));
    }

    /**
     * @return list<array{
     *     phpValue: array|null,
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
                'phpValue' => [],
                'postgresValue' => '{}',
            ],
            [
                'phpValue' => [681, 1185, 1878, 1989],
                'postgresValue' => '{681,1185,1878,1989}',
            ],
        ];
    }

    #[Test]
    public function throws_exception_when_php_value_is_not_array(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/Given PHP value content type is not PHP array. Instead it is "\w+"./');

        $this->fixture->convertToDatabaseValue('invalid-php-value-type', $this->platform); // @phpstan-ignore argument.type
    }

    #[Test]
    public function throws_domain_exception_when_invalid_array_item_value(): void
    {
        $this->expectException(ConversionException::class);
        $this->expectExceptionMessage("One or more of the items given doesn't look valid.");

        $this->fixture
            ->expects($this->once())
            ->method('isValidArrayItemForDatabase')
            ->willReturn(false);

        $this->fixture->convertToDatabaseValue([0], $this->platform);
    }

    #[Test]
    public function throws_domain_exception_when_postgres_value_is_not_valid_php_array(): void
    {
        $this->expectException(ConversionException::class);
        $this->expectExceptionMessageMatches('/Given PostgreSQL value content type is not PHP string. Instead it is "\w+"./');

        $this->fixture->convertToPHPValue(681, $this->platform); // @phpstan-ignore argument.type
    }
}
