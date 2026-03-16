<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidLsegForDatabaseException;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidLsegForPHPException;
use MartinGeorgiev\Doctrine\DBAL\Types\Lseg;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Lseg as LsegValueObject;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class LsegTest extends TestCase
{
    /**
     * @var AbstractPlatform&MockObject
     */
    private MockObject $platform;

    private Lseg $fixture;

    protected function setUp(): void
    {
        $this->platform = $this->createMock(AbstractPlatform::class);
        $this->fixture = new Lseg();
    }

    #[Test]
    public function has_name(): void
    {
        $this->assertSame('lseg', $this->fixture->getName());
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
    public function can_transform_from_php_value(LsegValueObject $lsegValueObject, string $postgresValue): void
    {
        $this->assertSame($postgresValue, $this->fixture->convertToDatabaseValue($lsegValueObject, $this->platform));
    }

    #[DataProvider('provideValidTransformations')]
    #[Test]
    public function can_transform_to_php_value(LsegValueObject $lsegValueObject, string $postgresValue): void
    {
        $result = $this->fixture->convertToPHPValue($postgresValue, $this->platform);
        $this->assertInstanceOf(LsegValueObject::class, $result);
        $this->assertSame($postgresValue, (string) $result);
    }

    /**
     * @return array<string, array{lsegValueObject: LsegValueObject, postgresValue: string}>
     */
    public static function provideValidTransformations(): array
    {
        return [
            'basic lseg with brackets' => [
                'lsegValueObject' => new LsegValueObject('[(1,2),(3,4)]'),
                'postgresValue' => '[(1,2),(3,4)]',
            ],
            'lseg with floats' => [
                'lsegValueObject' => new LsegValueObject('[(1.5,2.5),(3.5,4.5)]'),
                'postgresValue' => '[(1.5,2.5),(3.5,4.5)]',
            ],
            'lseg with negative coordinates' => [
                'lsegValueObject' => new LsegValueObject('[(-1,-2),(-3,-4)]'),
                'postgresValue' => '[(-1,-2),(-3,-4)]',
            ],
            'lseg without brackets' => [
                'lsegValueObject' => new LsegValueObject('(1,2),(3,4)'),
                'postgresValue' => '(1,2),(3,4)',
            ],
        ];
    }

    #[DataProvider('provideInvalidPHPValueInputs')]
    #[Test]
    public function throws_exception_for_invalid_php_value_inputs(mixed $phpValue): void
    {
        $this->expectException(InvalidLsegForPHPException::class);
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
            'array input' => [['not', 'lseg']],
            'boolean input' => [true],
            'object input' => [new \stdClass()],
        ];
    }

    #[DataProvider('provideInvalidDatabaseValueInputs')]
    #[Test]
    public function throws_exception_for_invalid_database_value_inputs(mixed $dbValue): void
    {
        $this->expectException(InvalidLsegForDatabaseException::class);
        $this->fixture->convertToPHPValue($dbValue, $this->platform);
    }

    /**
     * @return array<string, array{mixed}>
     */
    public static function provideInvalidDatabaseValueInputs(): array
    {
        return [
            'empty string' => [''],
            'invalid format' => ['not a lseg'],
            'only one point' => ['(1,2)'],
            'integer input' => [123],
            'array input' => [['not', 'lseg']],
            'boolean input' => [false],
            'object input' => [new \stdClass()],
        ];
    }
}
