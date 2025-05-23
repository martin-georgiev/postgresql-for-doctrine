<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidPointArrayItemForPHPException;
use MartinGeorgiev\Doctrine\DBAL\Types\PointArray;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Point;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class PointArrayTest extends TestCase
{
    /**
     * @var AbstractPlatform&MockObject
     */
    private MockObject $platform;

    private PointArray $fixture;

    protected function setUp(): void
    {
        $this->platform = $this->createMock(AbstractPlatform::class);
        $this->fixture = new PointArray();
    }

    #[Test]
    public function has_name(): void
    {
        self::assertEquals('point[]', $this->fixture->getName());
    }

    #[DataProvider('provideValidTransformations')]
    #[Test]
    public function can_transform_from_php_value(?array $phpValue, ?string $postgresValue): void
    {
        self::assertEquals($postgresValue, $this->fixture->convertToDatabaseValue($phpValue, $this->platform));
    }

    #[DataProvider('provideValidTransformations')]
    #[Test]
    public function can_transform_to_php_value(?array $phpValue, ?string $postgresValue): void
    {
        self::assertEquals($phpValue, $this->fixture->convertToPHPValue($postgresValue, $this->platform));
    }

    /**
     * @return array<string, array{phpValue: array<Point>|null, postgresValue: string|null}>
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
            'single point' => [
                'phpValue' => [new Point(1.23, 4.56)],
                'postgresValue' => '{"(1.230000, 4.560000)"}',
            ],
            'multiple points' => [
                'phpValue' => [
                    new Point(1.23, 4.56),
                    new Point(-7.89, 0.12),
                ],
                'postgresValue' => '{"(1.230000, 4.560000)","(-7.890000, 0.120000)"}',
            ],
            'points with zero values' => [
                'phpValue' => [
                    new Point(0.0, 0.0),
                    new Point(10.5, -3.7),
                ],
                'postgresValue' => '{"(0.000000, 0.000000)","(10.500000, -3.700000)"}',
            ],
        ];
    }

    #[DataProvider('provideInvalidTransformations')]
    #[Test]
    public function throws_exception_when_invalid_data_provided_to_convert_to_database_value(mixed $phpValue): void
    {
        $this->expectException(InvalidPointArrayItemForPHPException::class);
        $this->fixture->convertToDatabaseValue($phpValue, $this->platform); // @phpstan-ignore-line
    }

    /**
     * @return array<string, array{mixed}>
     */
    public static function provideInvalidTransformations(): array
    {
        return [
            'not an array' => ['string value'],
            'array containing non-Point items' => [[1, 2, 3]],
            'invalid nested point' => [['(1.23,4.56)']],
            'mixed array (valid and invalid points)' => [
                [
                    new Point(1.23, 4.56),
                    'invalid',
                ],
            ],
        ];
    }

    #[DataProvider('provideInvalidDatabaseValues')]
    #[Test]
    public function throws_exception_when_invalid_data_provided_to_convert_to_php_value(string $postgresValue): void
    {
        $this->expectException(InvalidPointArrayItemForPHPException::class);
        $this->fixture->convertToPHPValue($postgresValue, $this->platform);
    }

    /**
     * @return array<string, array{string}>
     */
    public static function provideInvalidDatabaseValues(): array
    {
        return [
            'missing parentheses' => ['{"(1.23, 4.56)","(-7.89, 0.12"}'],
            'non-numeric values' => ['{"(abc, 4.56)","(-7.89, 0.12"}'],
            'too many coordinates' => ['{"(1.23, 4.56, 7,89)","(-7.89, 0.12"}'],
            'invalid array format' => ['{"(1.23,4.56)","(a,b)"}'],
            'invalid characters' => ['{"(1.23, 4.56)","(-7.89, @,?)"}'],
        ];
    }

    #[DataProvider('provideInvalidPHPValueTypes')]
    #[Test]
    public function throws_exception_when_non_string_provided_to_convert_to_php_value(mixed $value): void
    {
        $this->expectException(InvalidPointArrayItemForPHPException::class);
        $this->fixture->convertToDatabaseValue($value, $this->platform); // @phpstan-ignore-line
    }

    /**
     * @return array<string, array{mixed}>
     */
    public static function provideInvalidPHPValueTypes(): array
    {
        return [
            'integer' => [123],
            'array' => [['(1.23, 4.56)']],
            'object' => [new \stdClass()],
            'boolean' => [true],
        ];
    }

    #[Test]
    public function throws_exception_when_invalid_point_format_provided(): void
    {
        $this->expectException(InvalidPointArrayItemForPHPException::class);

        $invalidPointString = '(invalid,point)';
        $this->fixture->transformArrayItemForPHP($invalidPointString);
    }

    #[Test]
    public function throws_exception_for_malformed_point_strings_in_database(): void
    {
        $this->expectException(InvalidPointArrayItemForPHPException::class);

        // This triggers the invalid format path without using reflection
        $this->fixture->convertToPHPValue('{"(invalid,point)"}', $this->platform);
    }

    #[Test]
    public function handles_edge_case_with_empty_and_malformed_arrays(): void
    {
        $result1 = $this->fixture->convertToPHPValue('{}', $this->platform);
        $result2 = $this->fixture->convertToPHPValue('{invalid}', $this->platform);
        $result3 = $this->fixture->convertToPHPValue('{""}', $this->platform);

        self::assertEquals([], $result1);
        self::assertEquals([], $result2);
        self::assertEquals([], $result3);
    }

    #[Test]
    public function returns_empty_array_for_non_standard_postgres_array_format(): void
    {
        $result1 = $this->fixture->convertToPHPValue('[test]', $this->platform);
        $result2 = $this->fixture->convertToPHPValue('not-an-array', $this->platform);

        self::assertEquals([], $result1);
        self::assertEquals([], $result2);
    }

    #[Test]
    public function transform_array_item_for_php_returns_null_for_null(): void
    {
        $this->assertNull($this->fixture->transformArrayItemForPHP(null));
    }

    #[Test]
    #[DataProvider('provideInvalidTypes')]
    public function transform_array_item_for_php_throws_for_invalid_type(mixed $value): void
    {
        $this->expectException(InvalidPointArrayItemForPHPException::class);
        $this->fixture->transformArrayItemForPHP($value);
    }

    #[Test]
    #[DataProvider('provideInvalidTypes')]
    public function transform_postgres_array_to_php_array_returns_empty_for_invalid_format(mixed $value): void
    {
        $reflectionObject = new \ReflectionObject($this->fixture);
        $reflectionMethod = $reflectionObject->getMethod('transformPostgresArrayToPHPArray');
        $reflectionMethod->setAccessible(true);
        $this->assertSame([], $reflectionMethod->invoke($this->fixture, $value));
    }

    public static function provideInvalidTypes(): array
    {
        return [
            [123],
            ['not-a-point-instance'],
            ['{invalid}'],
        ];
    }
}
