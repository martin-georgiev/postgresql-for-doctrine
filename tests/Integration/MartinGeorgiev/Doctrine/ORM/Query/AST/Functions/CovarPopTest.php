<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\CovarPop;
use PHPUnit\Framework\Attributes\Test;

class CovarPopTest extends NumericTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'COVAR_POP' => CovarPop::class,
        ];
    }

    #[Test]
    public function returns_zero_for_single_row_set_on_integer_columns(): void
    {
        $dql = 'SELECT COVAR_POP(t.integer1, t.integer2) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics t';
        $result = $this->executeDqlQuery($dql);
        $this->assertEqualsWithDelta(0.0, $result[0]['result'], 0.0001);
    }

    #[Test]
    public function returns_zero_for_single_row_set_on_decimal_columns(): void
    {
        $dql = 'SELECT COVAR_POP(t.decimal1, t.decimal2) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics t';
        $result = $this->executeDqlQuery($dql);
        $this->assertEqualsWithDelta(0.0, $result[0]['result'], 0.0001);
    }
}
