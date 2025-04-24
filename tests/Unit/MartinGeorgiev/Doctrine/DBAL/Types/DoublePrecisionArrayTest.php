<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Types\DoublePrecisionArray;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidFloatArrayItemForPHPException;

class DoublePrecisionArrayTest extends BaseFloatArrayTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->fixture = new DoublePrecisionArray();
    }

    /**
     * @test
     */
    public function has_name(): void
    {
        self::assertEquals('double precision[]', $this->fixture->getName());
    }

    public static function provideInvalidPHPValuesForDatabaseTransformation(): array
    {
        return \array_merge(parent::provideInvalidPHPValuesForDatabaseTransformation(), [
            ['1.7976931348623157E+309'], // Too large
            ['-1.7976931348623157E+309'], // Too small
            ['1.123456789012345678'], // Too many decimal places (>15)
            ['2.2250738585072014E-309'], // Too close to zero
            ['-2.2250738585072014E-309'], // Too close to zero (negative)
        ]);
    }

    /**
     * @return array<int, array{phpValue: float, postgresValue: string}>
     */
    public static function provideValidTransformations(): array
    {
        return [
            ['phpValue' => 1.23e4, 'postgresValue' => '1.23e4'],
            ['phpValue' => 1.23e-4, 'postgresValue' => '1.23e-4'],
            ['phpValue' => 1.234567890123456, 'postgresValue' => '1.234567890123456'],
            ['phpValue' => 1., 'postgresValue' => '1.0'],
            ['phpValue' => 1.0, 'postgresValue' => '1.0'],
            ['phpValue' => -1.0, 'postgresValue' => '-1.0'],
        ];
    }

    /**
     * @test
     */
    public function throws_domain_exception_when_value_is_too_close_to_zero(): void
    {
        $this->expectException(InvalidFloatArrayItemForPHPException::class);
        $this->expectExceptionMessage('is too close to zero for PostgreSQL double precision[] type');

        $this->fixture->transformArrayItemForPHP('1.18E-308');
    }

    /**
     * @test
     */
    public function throws_domain_exception_when_value_exceeds_precision_limit(): void
    {
        $this->expectException(InvalidFloatArrayItemForPHPException::class);
        $this->expectExceptionMessage('exceeds maximum precision for PostgreSQL double precision[] type');

        $this->fixture->transformArrayItemForPHP('1.123456789012345678');
    }

    /**
     * @test
     *
     * @dataProvider providePrecisionExceedingValues
     */
    public function throws_domain_exception_for_various_precision_violations(string $value): void
    {
        $this->expectException(InvalidFloatArrayItemForPHPException::class);
        $this->expectExceptionMessage('exceeds maximum precision for PostgreSQL double precision[] type');

        $this->fixture->transformArrayItemForPHP($value);
    }

    /**
     * @return array<string, array{string}>
     */
    public static function providePrecisionExceedingValues(): array
    {
        return [
            'sixteen decimals' => ['1.1234567890123456789'],
            'many trailing zeros' => ['1.123456789012345000000'],
            'large number with excess precision' => ['123456.1234567890123456789'],
            'negative with excess precision' => ['-1.1234567890123456789'],
        ];
    }
}
