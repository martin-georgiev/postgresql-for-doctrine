<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Cot;
use PHPUnit\Framework\Attributes\Test;

class CotTest extends NumericTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'COT' => Cot::class,
        ];
    }

    #[Test]
    public function cot_of_pi_over_4(): void
    {
        $dql = 'SELECT COT(0.7853981633974483) as result FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics n WHERE n.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertEqualsWithDelta(1.0, $result[0]['result'], 0.000001);
    }

    #[Test]
    public function cot_with_entity_property(): void
    {
        $dql = 'SELECT COT(n.decimal1) as result FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics n WHERE n.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertEqualsWithDelta(0.54056976244981, $result[0]['result'], 0.000001);
    }
}
