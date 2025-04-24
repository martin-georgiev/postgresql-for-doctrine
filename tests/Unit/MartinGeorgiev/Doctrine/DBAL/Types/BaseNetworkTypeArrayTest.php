<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use MartinGeorgiev\Doctrine\DBAL\Types\BaseNetworkTypeArray;
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

            protected function throwInvalidItemException(): never
            {
                throw new \InvalidArgumentException('Invalid item');
            }
        };
    }

    /**
     * @test
     */
    public function has_name(): void
    {
        self::assertEquals('test_network_array', $this->fixture->getName());
    }

    /**
     * @test
     */
    public function can_convert_null_to_database_value(): void
    {
        self::assertNull($this->fixture->convertToDatabaseValue(null, $this->platform));
    }

    /**
     * @test
     */
    public function can_convert_empty_array_to_database_value(): void
    {
        self::assertEquals('{}', $this->fixture->convertToDatabaseValue([], $this->platform));
    }

    /**
     * @test
     */
    public function can_convert_valid_array_to_database_value(): void
    {
        self::assertEquals(
            '{"valid_address","valid_address"}',
            $this->fixture->convertToDatabaseValue(['valid_address', 'valid_address'], $this->platform)
        );
    }

    /**
     * @test
     */
    public function throws_exception_for_invalid_type(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid type');
        $this->fixture->convertToDatabaseValue('not_an_array', $this->platform);
    }

    /**
     * @test
     */
    public function can_convert_null_to_php_value(): void
    {
        self::assertNull($this->fixture->convertToPHPValue(null, $this->platform));
    }

    /**
     * @test
     */
    public function can_convert_empty_array_to_php_value(): void
    {
        self::assertEquals([], $this->fixture->convertToPHPValue('{}', $this->platform));
    }

    /**
     * @test
     */
    public function can_convert_valid_string_to_php_value(): void
    {
        self::assertEquals(
            ['valid_address', 'valid_address'],
            $this->fixture->convertToPHPValue('{"valid_address","valid_address"}', $this->platform)
        );
    }
}
