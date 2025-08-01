<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Pi;

class PiTest extends NumericTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'PI' => Pi::class,
        ];
    }

    public function test_pi(): void
    {
        $dql = 'SELECT PI() as result FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics t WHERE t.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertEqualsWithDelta(3.141592653589793, $result[0]['result'], 0.0001);
    }

    public function test_pi_plus_entity_property(): void
    {
        $dql = 'SELECT PI() + n.decimal1 as result FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics n WHERE n.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertEqualsWithDelta(13.641592653589793, $result[0]['result'], 0.000001);
    }
}
