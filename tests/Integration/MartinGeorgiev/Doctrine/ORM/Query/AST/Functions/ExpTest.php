<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Exp;

class ExpTest extends NumericTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'EXP' => Exp::class,
        ];
    }

    public function test_exp(): void
    {
        $dql = 'SELECT EXP(1) as result FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsJsons t WHERE t.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertEqualsWithDelta(2.718281828459, $result[0]['result'], 0.0001);
    }

    public function test_exp_with_entity_property(): void
    {
        $dql = 'SELECT EXP(n.decimal1) as result FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics n WHERE n.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertSame(1, $result[0]['result']);
    }
}
