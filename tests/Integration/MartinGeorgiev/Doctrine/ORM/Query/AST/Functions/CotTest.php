<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Cot;
use PHPUnit\Framework\Attributes\Test;

final class CotTest extends NumericTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'COT' => Cot::class,
        ];
    }

    #[Test]
    public function calculates_cot_of_literal(): void
    {
        $dql = 'SELECT COT(1) as result FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics n WHERE n.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(0.6420926159343306, $result[0]['result']);
    }

    #[Test]
    public function calculates_cot_with_entity_property(): void
    {
        $dql = 'SELECT COT(n.decimal1) as result FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics n WHERE n.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(0.5405697624498119, $result[0]['result']);
    }
}
