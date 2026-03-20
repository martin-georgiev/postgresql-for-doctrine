<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\GenerateNumericSeries;
use PHPUnit\Framework\Attributes\Test;

class GenerateNumericSeriesTest extends NumericTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'GENERATE_NUMERIC_SERIES' => GenerateNumericSeries::class,
        ];
    }

    #[Test]
    public function can_generate_integer_series_without_explicit_step(): void
    {
        $dql = 'SELECT GENERATE_NUMERIC_SERIES(t.integer1, t.integer2) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics t
                WHERE t.id = 1';

        $result = $this->executeDqlQuery($dql);

        $this->assertCount(11, $result);

        $this->assertIsInt($result[0]['result']);
        $this->assertSame(10, $result[0]['result']);

        $this->assertIsInt($result[10]['result']);
        $this->assertSame(20, $result[10]['result']);
    }

    #[Test]
    public function can_generate_bigint_series_with_explicit_step(): void
    {
        $dql = 'SELECT GENERATE_NUMERIC_SERIES(t.bigint1, t.bigint2, 1000) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics t
                WHERE t.id = 1';

        $result = $this->executeDqlQuery($dql);

        $this->assertCount(2, $result);

        $this->assertIsInt($result[0]['result']);
        $this->assertSame(1000, $result[0]['result']);

        $this->assertIsInt($result[1]['result']);
        $this->assertSame(2000, $result[1]['result']);
    }

    #[Test]
    public function can_generate_decimal_series_without_explicit_step(): void
    {
        $dql = 'SELECT GENERATE_NUMERIC_SERIES(t.decimal1, t.decimal2) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics t
                WHERE t.id = 1';

        $result = $this->executeDqlQuery($dql);

        $this->assertCount(11, $result);

        $this->assertIsString($result[0]['result']);
        $this->assertEqualsWithDelta(10.5, (float) $result[0]['result'], 0.001);

        $this->assertIsString($result[10]['result']);
        $this->assertEqualsWithDelta(20.5, (float) $result[10]['result'], 0.001);
    }
}
