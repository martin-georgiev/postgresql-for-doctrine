<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Exp;
use PHPUnit\Framework\Attributes\Test;

final class ExpTest extends NumericTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'EXP' => Exp::class,
        ];
    }

    #[Test]
    public function returns_the_exponential_from_a_numeric_literal(): void
    {
        $dql = 'SELECT EXP(1) as result FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics t WHERE t.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(2.718281828459045, $result[0]['result']);
    }

    #[Test]
    public function returns_the_exponential_from_an_entity_field(): void
    {
        $dql = 'SELECT EXP(n.decimal1) as result FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics n WHERE n.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(36315.502674246638, $result[0]['result']);
    }
}
