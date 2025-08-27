<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Ln;
use PHPUnit\Framework\Attributes\Test;

class LnTest extends NumericTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'LN' => Ln::class,
        ];
    }

    #[Test]
    public function can_calculate_natural_logarithm_of_euler_constant(): void
    {
        $dql = 'SELECT LN(2.718281828459) as result FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics t WHERE t.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertEqualsWithDelta(1.0, $result[0]['result'], 0.0001);
    }

    #[Test]
    public function can_calculate_natural_logarithm_of_entity_decimal_value(): void
    {
        $dql = 'SELECT LN(n.decimal1) as result FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics n WHERE n.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertEqualsWithDelta(2.3513752571634777, $result[0]['result'], 0.000001);
    }
}
