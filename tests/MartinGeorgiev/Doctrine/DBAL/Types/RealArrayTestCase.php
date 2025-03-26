<?php

declare(strict_types=1);

namespace Tests\MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidFloatValueException;
use MartinGeorgiev\Doctrine\DBAL\Types\RealArray;

class RealArrayTestCase extends BaseFloatArrayTestCase
{
    protected function setUp(): void
    {
        $this->fixture = new RealArray();
    }

    /**
     * @test
     */
    public function has_name(): void
    {
        self::assertEquals('real[]', $this->fixture->getName());
    }

    public static function provideInvalidTransformations(): array
    {
        return \array_merge(parent::provideInvalidTransformations(), [
            ['3.402823467E+38'], // Too large
            ['-3.402823467E+38'], // Too small
            ['1.1234567'], // Too many decimal places (>6)
            ['1e38'], // Scientific notation not allowed
            ['1.17E-38'], // Too close to zero
            ['-1.17E-38'], // Too close to zero (negative)
        ]);
    }

    /**
     * @return array<int, array{phpValue: float, postgresValue: string}>
     */
    public static function provideValidTransformations(): array
    {
        return [
            [
                'phpValue' => -3.402823466E+38,
                'postgresValue' => '-340282346600000000000000000000000000000',
            ],
            [
                'phpValue' => 3.402823466E+38,
                'postgresValue' => '340282346600000000000000000000000000000',
            ],
            [
                'phpValue' => 1.123456,
                'postgresValue' => '1.123456',
            ],
            [
                'phpValue' => -1.123456,
                'postgresValue' => '-1.123456',
            ],
            [
                'phpValue' => 0.0,
                'postgresValue' => '0',
            ],
        ];
    }

    /**
     * @test
     */
    public function throws_conversion_exception_when_value_too_close_to_zero(): void
    {
        $this->expectException(InvalidFloatValueException::class);
        $this->expectExceptionMessage("Given value of '1.17E-38' is too close to zero for PostgreSQL REAL type");

        $this->fixture->transformArrayItemForPHP('1.17E-38');
    }
}
