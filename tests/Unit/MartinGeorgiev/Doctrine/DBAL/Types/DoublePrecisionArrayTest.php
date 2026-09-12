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
            'too many decimal places' => ['1.123456789012345678'],
            'sixteen decimals' => ['1.1234567890123456789'],
            'many trailing zeros' => ['1.123456789012345000000'],
            'large number with excess precision' => ['123456.1234567890123456789'],
            'negative with excess precision' => ['-1.1234567890123456789'],
            'too close to zero' => ['2.2250738585072014E-309'],
            'too close to zero (negative)' => ['-2.2250738585072014E-309'],
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
            ['phpValue' => 1.23e4, 'postgresValue' => '1.23e4'],
            ['phpValue' => 1.23e-4, 'postgresValue' => '1.23e-4'],
            ['phpValue' => 1.234567890123456, 'postgresValue' => '1.234567890123456'],
            ['phpValue' => 1., 'postgresValue' => '1.0'],
            ['phpValue' => 1.0, 'postgresValue' => '1.0'],
            ['phpValue' => -1.0, 'postgresValue' => '-1.0'],
        ];
    }

    public static function providePostgresOutputValues(): array
    {
        return [
            'seventeen significant digits' => ['postgresValue' => '0.12345678901234566', 'phpValue' => 0.12345678901234566],
            'trailing zeros beyond the precision limit' => ['postgresValue' => '1.123456789012345000000', 'phpValue' => 1.123456789012345],
            'sixteen decimals' => ['postgresValue' => '1.1234567890123456789', 'phpValue' => 1.1234567890123457],
            'eighteen decimals' => ['postgresValue' => '1.123456789012345678', 'phpValue' => 1.1234567890123457],
            'large number with excess precision' => ['postgresValue' => '123456.1234567890123456789', 'phpValue' => 123456.123456789],
            'negative with excess precision' => ['postgresValue' => '-1.1234567890123456789', 'phpValue' => -1.1234567890123457],
            'below the minimum normal magnitude' => ['postgresValue' => '1.18E-308', 'phpValue' => 1.18E-308],
            'scientific notation with a negative exponent' => ['postgresValue' => '1.5e-20', 'phpValue' => 1.5E-20],
            'scientific notation at the upper bound' => ['postgresValue' => '1.7976931348623157e+308', 'phpValue' => 1.7976931348623157E+308],
            'subnormal' => ['postgresValue' => '5e-324', 'phpValue' => 5.0E-324],
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
