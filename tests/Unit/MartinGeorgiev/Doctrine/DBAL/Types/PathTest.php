<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidPathForDatabaseException;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidPathForPHPException;
use MartinGeorgiev\Doctrine\DBAL\Types\Path;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Path as PathValueObject;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class PathTest extends TestCase
{
    /**
     * @var AbstractPlatform&MockObject
     */
    private MockObject $platform;

    private Path $fixture;

    protected function setUp(): void
    {
        $this->platform = $this->createMock(AbstractPlatform::class);
        $this->fixture = new Path();
    }

    #[Test]
    public function has_name(): void
    {
        $this->assertSame('path', $this->fixture->getName());
    }

    #[Test]
    public function converts_null_to_database(): void
    {
        $this->assertNull($this->fixture->convertToDatabaseValue(null, $this->platform));
    }

    #[Test]
    public function converts_null_from_database(): void
    {
        $this->assertNull($this->fixture->convertToPHPValue(null, $this->platform));
    }

    #[DataProvider('provideValidTransformations')]
    #[Test]
    public function can_transform_from_php_value(PathValueObject $pathValueObject, string $postgresValue): void
    {
        $this->assertSame($postgresValue, $this->fixture->convertToDatabaseValue($pathValueObject, $this->platform));
    }

    #[DataProvider('provideValidTransformations')]
    #[Test]
    public function can_transform_to_php_value(PathValueObject $pathValueObject, string $postgresValue): void
    {
        $result = $this->fixture->convertToPHPValue($postgresValue, $this->platform);
        $this->assertInstanceOf(PathValueObject::class, $result);
        $this->assertSame($postgresValue, (string) $result);
    }

    /**
     * @return array<string, array{pathValueObject: PathValueObject, postgresValue: string}>
     */
    public static function provideValidTransformations(): array
    {
        return [
            'open path with two points' => [
                'pathValueObject' => PathValueObject::fromString('[(1,2),(3,4)]'),
                'postgresValue' => '[(1,2),(3,4)]',
            ],
            'closed path with two points' => [
                'pathValueObject' => PathValueObject::fromString('((1,2),(3,4))'),
                'postgresValue' => '((1,2),(3,4))',
            ],
            'open path with three points' => [
                'pathValueObject' => PathValueObject::fromString('[(1,2),(3,4),(5,6)]'),
                'postgresValue' => '[(1,2),(3,4),(5,6)]',
            ],
            'path with floats' => [
                'pathValueObject' => PathValueObject::fromString('[(1.5,2.5),(3.5,4.5)]'),
                'postgresValue' => '[(1.5,2.5),(3.5,4.5)]',
            ],
            'path with negative coordinates' => [
                'pathValueObject' => PathValueObject::fromString('[(-1,-2),(-3,-4)]'),
                'postgresValue' => '[(-1,-2),(-3,-4)]',
            ],
        ];
    }

    #[DataProvider('provideInvalidPHPValueInputs')]
    #[Test]
    public function throws_exception_for_invalid_php_value_inputs(mixed $phpValue): void
    {
        $this->expectException(InvalidPathForPHPException::class);
        $this->fixture->convertToDatabaseValue($phpValue, $this->platform);
    }

    /**
     * @return array<string, array{mixed}>
     */
    public static function provideInvalidPHPValueInputs(): array
    {
        return [
            'string input' => ['[(1,2),(3,4)]'],
            'integer input' => [123],
            'array input' => [['not', 'path']],
            'boolean input' => [true],
            'object input' => [new \stdClass()],
        ];
    }

    #[DataProvider('provideInvalidDatabaseValueInputs')]
    #[Test]
    public function throws_exception_for_invalid_database_value_inputs(mixed $dbValue): void
    {
        $this->expectException(InvalidPathForDatabaseException::class);
        $this->fixture->convertToPHPValue($dbValue, $this->platform);
    }

    /**
     * @return array<string, array{mixed}>
     */
    public static function provideInvalidDatabaseValueInputs(): array
    {
        return [
            'empty string' => [''],
            'invalid format' => ['not a path'],
            'bare point list' => ['(1,2),(3,4)'],
            'integer input' => [123],
            'array input' => [['not', 'path']],
            'boolean input' => [false],
            'object input' => [new \stdClass()],
        ];
    }
}
