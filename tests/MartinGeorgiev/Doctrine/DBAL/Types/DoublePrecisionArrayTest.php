<?php

declare(strict_types=1);

namespace Tests\MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Types\DoublePrecisionArray;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidFloatValueException;

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

    public static function provideInvalidTransformations(): array
    {
        return \array_merge(parent::provideInvalidTransformations(), [
            ['1.7976931348623157E+309'], // Too large
            ['-1.7976931348623157E+309'], // Too small
            ['1.123456789012345678'], // Too many decimal places (>15)
            ['2.2250738585072014E-309'], // Too close to zero
            ['-2.2250738585072014E-309'], // Too close to zero (negative)
            ['not_a_number'],
            ['1.23.45'],
            ['1e'], // Invalid scientific notation
            ['e1'], // Invalid scientific notation
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
    public function throws_conversion_exception_when_value_is_too_close_to_zero(): void
    {
        $this->expectException(InvalidFloatValueException::class);
        $this->expectExceptionMessage("Given value of '1.18E-308' is too close to zero for PostgreSQL double precision[] type");

        $this->fixture->transformArrayItemForPHP('1.18E-308');
    }
}
