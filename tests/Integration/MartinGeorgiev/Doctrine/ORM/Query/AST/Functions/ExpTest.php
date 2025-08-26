<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Exp;
use PHPUnit\Framework\Attributes\Test;

class ExpTest extends NumericTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'EXP' => Exp::class,
        ];
    }

    #[Test]
    public function exp(): void
    {
        $dql = 'SELECT EXP(1) as result FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics t WHERE t.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertEqualsWithDelta(2.718281828459, $result[0]['result'], 0.0001);
    }

    #[Test]
    public function exp_with_entity_property(): void
    {
        $dql = 'SELECT EXP(n.decimal1) as result FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics n WHERE n.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertEqualsWithDelta(36315.502674246638, $result[0]['result'], 0.000001);
    }
}
