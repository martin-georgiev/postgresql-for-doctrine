<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use MartinGeorgiev\Doctrine\DBAL\Types\BaseNetworkTypeArray;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class BaseNetworkTypeArrayTest extends TestCase
{
    /**
     * @var AbstractPlatform&MockObject
     */
    private MockObject $platform;

    private BaseNetworkTypeArray $fixture;

    protected function setUp(): void
    {
        $this->platform = $this->createMock(AbstractPlatform::class);
        // Create a concrete implementation of the abstract class for testing
        $this->fixture = new class extends BaseNetworkTypeArray {
            protected const TYPE_NAME = 'test_network_array';

            protected function isValidNetworkAddress(string $value): bool
            {
                return $value === 'valid_address';
            }

            protected function throwInvalidTypeException(mixed $value): never
            {
                throw new \InvalidArgumentException('Invalid type');
            }

            protected function throwInvalidFormatException(mixed $value): never
            {
                throw new \InvalidArgumentException('Invalid format');
            }

            protected function throwInvalidItemException(mixed $item): never
            {
                throw new \InvalidArgumentException('Invalid item: '.\var_export($item, true));
            }
        };
    }

    #[Test]
    public function has_name(): void
    {
        $this->assertSame('test_network_array', $this->fixture->getName());
    }

    #[Test]
    #[DataProvider('provideValidTransformations')]
    public function can_transform_to_php_value(?array $phpValue, ?string $postgresValue): void
    {
        $this->assertSame($phpValue, $this->fixture->convertToPHPValue($postgresValue, $this->platform));
    }

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
            'valid array' => [
                'phpValue' => ['valid_address', 'valid_address'],
                'postgresValue' => '{"valid_address","valid_address"}',
            ],
        ];
    }

    #[Test]
    public function throws_exception_for_invalid_type(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid type');
        $this->fixture->convertToDatabaseValue('not_an_array', $this->platform); // @phpstan-ignore argument.type
    }

    #[Test]
    #[DataProvider('provideInvalidValues')]
    public function throws_exception_for_invalid_values(mixed $arrayItem, string $exceptionMessage): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage($exceptionMessage);
        $this->fixture->transformArrayItemForPHP($arrayItem);
    }

    public static function provideInvalidValues(): array
    {
        return [
            'invalid type' => [
                'arrayItem' => [],
                'exceptionMessage' => 'Invalid type',
            ],
            'invalid format' => [
                'arrayItem' => '"invalid_address"',
                'exceptionMessage' => 'Invalid format',
            ],
        ];
    }

    #[Test]
    public function can_transform_array_item_for_php_with_valid_string(): void
    {
        $this->assertSame('valid_address', $this->fixture->transformArrayItemForPHP('"valid_address"'));
    }
}
