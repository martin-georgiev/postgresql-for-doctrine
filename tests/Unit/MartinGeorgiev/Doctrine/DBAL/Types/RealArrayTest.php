<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidFloatArrayItemForPHPException;
use MartinGeorgiev\Doctrine\DBAL\Types\RealArray;
use PHPUnit\Framework\Attributes\Test;

final class RealArrayTest extends BaseFloatArrayTestCase
{
    protected function setUp(): void
    {
        $this->fixture = new RealArray();
    }

    #[Test]
    public function has_name(): void
    {
        $this->assertSame('real[]', $this->fixture->getName());
    }

    public static function provideInvalidDatabaseValueInputs(): array
    {
        return \array_merge(parent::provideInvalidDatabaseValueInputs(), [
            'too large' => ['3.5E+38'],
            'too small' => ['-3.5E+38'],
            'too many decimal places' => ['1.1234567'],
            'many trailing zeros' => ['1.123000000'],
            'large number with excess precision' => ['123456.1234567'],
            'negative with excess precision' => ['-1.1234567'],
            'too close to zero' => ['1.17E-38'],
            'too close to zero (negative)' => ['-1.17E-38'],
        ]);
    }

    /**
     * @return list<array{
     *     phpValue: float,
     *     postgresValue: string
     * }>
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

    #[Test]
    public function throws_exception_for_value_exceeding_range(): void
    {
        $this->expectException(InvalidFloatArrayItemForPHPException::class);
        $this->expectExceptionMessage('cannot be transformed to valid PHP float');

        $this->fixture->transformArrayItemForPHP('9999999999999999999999999999999999999999');
    }

    public static function providePostgresOutputValues(): array
    {
        return [
            'seven significant digits' => ['postgresValue' => '1.1234567', 'phpValue' => 1.1234567],
            'eight fractional digits' => ['postgresValue' => '0.12345679', 'phpValue' => 0.12345679],
            'trailing zeros beyond the precision limit' => ['postgresValue' => '1.123000000', 'phpValue' => 1.123],
            'large number with excess precision' => ['postgresValue' => '123456.1234567', 'phpValue' => 123456.1234567],
            'negative with excess precision' => ['postgresValue' => '-1.1234567', 'phpValue' => -1.1234567],
            'scientific notation at the upper bound' => ['postgresValue' => '3.4028235e+38', 'phpValue' => 3.4028235E+38],
            'below the minimum normal magnitude' => ['postgresValue' => '1.17E-38', 'phpValue' => 1.17E-38],
            'subnormal' => ['postgresValue' => '1e-45', 'phpValue' => 1.0E-45],
        ];
    }

    public static function provideValidScientificNotationStrings(): array
    {
        return [
            'no fractional part' => ['1e38'],
            'positive exponent' => ['1.5e2'],
            'negative exponent' => ['1.5e-20'],
            'explicit positive exponent' => ['3.4028235E+38'],
            'ten significant digits still inside the range' => ['3.402823467E+38'],
            'negative and inside the range' => ['-3.402823467E+38'],
        ];
    }

    #[Test]
    public function throws_exception_for_a_doubled_sign_before_a_non_finite_value(): void
    {
        $this->expectException(InvalidFloatArrayItemForPHPException::class);

        $this->fixture->convertToPHPValue('{++inf}', $this->createStub(AbstractPlatform::class));
    }

    #[Test]
    public function converts_non_finite_values_to_php_value(): void
    {
        $result = $this->fixture->convertToPHPValue('{Infinity,-Infinity,NaN,1.5}', $this->createStub(AbstractPlatform::class));

        $this->assertIsArray($result);
        $this->assertSame(\INF, $result[0]);
        $this->assertSame(-\INF, $result[1]);
        $this->assertNan($result[2]);
        $this->assertSame(1.5, $result[3]);
    }

    #[Test]
    public function converts_non_finite_values_to_database_value(): void
    {
        $this->assertSame(
            '{Infinity,-Infinity,NaN,1.5}',
            $this->fixture->convertToDatabaseValue([\INF, -\INF, \NAN, 1.5], $this->createStub(AbstractPlatform::class))
        );
    }
}
