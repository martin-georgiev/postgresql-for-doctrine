<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use MartinGeorgiev\Doctrine\DBAL\Types\DoublePrecisionArray;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidFloatArrayItemForPHPException;
use PHPUnit\Framework\Attributes\Test;

final class DoublePrecisionArrayTest extends BaseFloatArrayTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->fixture = new DoublePrecisionArray();
    }

    #[Test]
    public function has_name(): void
    {
        $this->assertSame('double precision[]', $this->fixture->getName());
    }

    public static function provideInvalidDatabaseValueInputs(): array
    {
        return \array_merge(parent::provideInvalidDatabaseValueInputs(), [
            'too large' => ['1.7976931348623157E+309'],
            'too small' => ['-1.7976931348623157E+309'],
            'rounds to zero in double precision' => ['2e-324'],
            'rounds to zero in double precision (negative)' => ['-2e-324'],
        ]);
    }

    /**
     * PostgreSQL rounds a value to the nearest double, so digits past double precision and subnormals are both stored.
     *
     * @return array<string, array{mixed}>
     */
    public static function provideValidArrayItemsForDatabase(): array
    {
        return \array_merge(parent::provideValidArrayItemsForDatabase(), [
            'more digits than double precision keeps' => ['1.123456789012345678'],
            'trailing zeros past double precision' => ['1.123456789012345000000'],
            'large number with more digits than double precision keeps' => ['123456.1234567890123456789'],
            'negative with more digits than double precision keeps' => ['-1.1234567890123456789'],
            'float with sixteen decimals' => [0.1234567890123456],
            'subnormal' => ['2.2250738585072014E-309'],
            'negative subnormal' => ['-2.2250738585072014E-309'],
            'smallest subnormal' => ['5e-324'],
        ]);
    }

    /**
     * @return array<string, array{postgresValue: string, expectedValue: float}>
     */
    public static function provideValidItemTransformationsToPHP(): array
    {
        return [
            'a positive exponent' => [
                'postgresValue' => '1.23e4',
                'expectedValue' => 1.23e4,
            ],
            'a negative exponent' => [
                'postgresValue' => '1.23e-4',
                'expectedValue' => 1.23e-4,
            ],
            'sixteen significant digits' => [
                'postgresValue' => '1.234567890123456',
                'expectedValue' => 1.234567890123456,
            ],
            'one with a fractional zero' => [
                'postgresValue' => '1.0',
                'expectedValue' => 1.0,
            ],
            'minus one' => [
                'postgresValue' => '-1.0',
                'expectedValue' => -1.0,
            ],
            'seventeen significant digits' => [
                'postgresValue' => '0.12345678901234566',
                'expectedValue' => 0.12345678901234566,
            ],
            'trailing zeros beyond the precision limit' => [
                'postgresValue' => '1.123456789012345000000',
                'expectedValue' => 1.123456789012345,
            ],
            'sixteen decimals' => [
                'postgresValue' => '1.1234567890123456789',
                'expectedValue' => 1.1234567890123457,
            ],
            'eighteen decimals' => [
                'postgresValue' => '1.123456789012345678',
                'expectedValue' => 1.1234567890123457,
            ],
            'large number with excess precision' => [
                'postgresValue' => '123456.1234567890123456789',
                'expectedValue' => 123456.123456789,
            ],
            'negative with excess precision' => [
                'postgresValue' => '-1.1234567890123456789',
                'expectedValue' => -1.1234567890123457,
            ],
            'below the minimum normal magnitude' => [
                'postgresValue' => '1.18E-308',
                'expectedValue' => 1.18E-308,
            ],
            'scientific notation with a negative exponent' => [
                'postgresValue' => '1.5e-20',
                'expectedValue' => 1.5E-20,
            ],
            'scientific notation at the upper bound' => [
                'postgresValue' => '1.7976931348623157e+308',
                'expectedValue' => 1.7976931348623157E+308,
            ],
            'subnormal' => [
                'postgresValue' => '5e-324',
                'expectedValue' => 5.0E-324,
            ],
        ];
    }

    public static function provideValidScientificNotationStrings(): array
    {
        return [
            'no fractional part' => ['1e308'],
            'positive exponent' => ['1.5e2'],
            'negative exponent' => ['1.5e-20'],
            'explicit positive exponent' => ['1.7976931348623157E+308'],
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
