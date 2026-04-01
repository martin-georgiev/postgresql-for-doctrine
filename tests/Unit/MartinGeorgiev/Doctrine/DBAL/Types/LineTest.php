<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidLineForDatabaseException;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidLineForPHPException;
use MartinGeorgiev\Doctrine\DBAL\Types\Line;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Line as LineValueObject;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class LineTest extends TestCase
{
    /**
     * @var AbstractPlatform&MockObject
     */
    private MockObject $platform;

    private Line $fixture;

    protected function setUp(): void
    {
        $this->platform = $this->createMock(AbstractPlatform::class);
        $this->fixture = new Line();
    }

    #[Test]
    public function has_name(): void
    {
        $this->assertSame('line', $this->fixture->getName());
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
    public function can_transform_from_php_value(LineValueObject $lineValueObject, string $postgresValue): void
    {
        $this->assertSame($postgresValue, $this->fixture->convertToDatabaseValue($lineValueObject, $this->platform));
    }

    #[DataProvider('provideValidTransformations')]
    #[Test]
    public function can_transform_to_php_value(LineValueObject $lineValueObject, string $postgresValue): void
    {
        $result = $this->fixture->convertToPHPValue($postgresValue, $this->platform);
        $this->assertInstanceOf(LineValueObject::class, $result);
        $this->assertEquals($lineValueObject, $result);
        $this->assertSame($postgresValue, (string) $result);
    }

    /**
     * @return array<string, array{lineValueObject: LineValueObject, postgresValue: string}>
     */
    public static function provideValidTransformations(): array
    {
        return [
            'basic line' => [
                'lineValueObject' => LineValueObject::fromString('{1,2,3}'),
                'postgresValue' => '{1,2,3}',
            ],
            'line with floats' => [
                'lineValueObject' => LineValueObject::fromString('{1.5,2.5,3.5}'),
                'postgresValue' => '{1.5,2.5,3.5}',
            ],
            'line with negative coefficients' => [
                'lineValueObject' => LineValueObject::fromString('{-1,-2,-3}'),
                'postgresValue' => '{-1,-2,-3}',
            ],
            'line with zero constant' => [
                'lineValueObject' => LineValueObject::fromString('{1,2,0}'),
                'postgresValue' => '{1,2,0}',
            ],
        ];
    }

    #[DataProvider('provideInvalidPHPValueInputs')]
    #[Test]
    public function throws_exception_for_invalid_php_value_inputs(mixed $phpValue): void
    {
        $this->expectException(InvalidLineForPHPException::class);
        $this->fixture->convertToDatabaseValue($phpValue, $this->platform);
    }

    /**
     * @return array<string, array{mixed}>
     */
    public static function provideInvalidPHPValueInputs(): array
    {
        return [
            'string input' => ['{1,2,3}'],
            'integer input' => [123],
            'array input' => [['not', 'line']],
            'boolean input' => [true],
            'object input' => [new \stdClass()],
        ];
    }

    #[DataProvider('provideInvalidDatabaseValueInputs')]
    #[Test]
    public function throws_exception_for_invalid_database_value_inputs(mixed $dbValue): void
    {
        $this->expectException(InvalidLineForDatabaseException::class);
        $this->fixture->convertToPHPValue($dbValue, $this->platform);
    }

    /**
     * @return array<string, array{mixed}>
     */
    public static function provideInvalidDatabaseValueInputs(): array
    {
        return [
            'empty string' => [''],
            'invalid format' => ['not a line'],
            'missing braces' => ['1,2,3'],
            'too few coefficients' => ['{1,2}'],
            'degenerate line' => ['{0,0,1}'],
            'integer input' => [123],
            'array input' => [['not', 'line']],
            'boolean input' => [false],
            'object input' => [new \stdClass()],
        ];
    }
}
