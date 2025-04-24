<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidFloatArrayItemForPHPException;
use MartinGeorgiev\Doctrine\DBAL\Types\RealArray;

class RealArrayTest extends BaseFloatArrayTestCase
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

    public static function provideInvalidPHPValuesForDatabaseTransformation(): array
    {
        return \array_merge(parent::provideInvalidPHPValuesForDatabaseTransformation(), [
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
                'phpValue' => -3.402823466E+8,
                'postgresValue' => '-3.402823466E+8',
            ],
            [
                'phpValue' => 3.402823466E+8,
                'postgresValue' => '3.402823466E+8',
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
                'phpValue' => 1.,
                'postgresValue' => '1.0',
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
    public function throws_domain_exception_when_value_too_close_to_zero(): void
    {
        $this->expectException(InvalidFloatArrayItemForPHPException::class);
        $this->expectExceptionMessage('is too close to zero for PostgreSQL real[] type');

        $this->fixture->transformArrayItemForPHP('1.17E-38');
    }

    /**
     * @test
     */
    public function throws_domain_exception_when_value_exceeds_precision_limit(): void
    {
        $this->expectException(InvalidFloatArrayItemForPHPException::class);
        $this->expectExceptionMessage('exceeds maximum precision for PostgreSQL real[] type');

        $this->fixture->transformArrayItemForPHP('1.1234567');
    }

    /**
     * @test
     *
     * @dataProvider providePrecisionExceedingValues
     */
    public function throws_domain_exception_for_various_precision_violations(string $value): void
    {
        $this->expectException(InvalidFloatArrayItemForPHPException::class);
        $this->expectExceptionMessage('exceeds maximum precision for PostgreSQL real[] type');

        $this->fixture->transformArrayItemForPHP($value);
    }

    /**
     * @return array<string, array{string}>
     */
    public static function providePrecisionExceedingValues(): array
    {
        return [
            'seven decimals' => ['1.1234567'],
            'many trailing zeros' => ['1.123000000'],
            'large number with excess precision' => ['123456.1234567'],
            'negative with excess precision' => ['-1.1234567'],
        ];
    }
}
