<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\VarPop;
use PHPUnit\Framework\Attributes\Test;

class VarPopTest extends NumericTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'VAR_POP' => VarPop::class,
        ];
    }

    #[Test]
    public function returns_zero_for_single_row_set_on_integer_column(): void
    {
        $dql = 'SELECT VAR_POP(t.integer1) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics t';
        $result = $this->executeDqlQuery($dql);
        $this->assertEqualsWithDelta(0.0, $result[0]['result'], 0.0001);
    }

    #[Test]
    public function returns_zero_for_single_row_set_on_decimal_column(): void
    {
        $dql = 'SELECT VAR_POP(t.decimal1) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics t';
        $result = $this->executeDqlQuery($dql);
        $this->assertEqualsWithDelta(0.0, $result[0]['result'], 0.0001);
    }
}
