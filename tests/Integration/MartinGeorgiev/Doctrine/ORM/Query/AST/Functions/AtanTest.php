<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Atan;
use PHPUnit\Framework\Attributes\Test;

final class AtanTest extends NumericTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ATAN' => Atan::class,
        ];
    }

    #[Test]
    public function returns_the_arc_tangent_from_a_numeric_literal(): void
    {
        $dql = 'SELECT ATAN(1.0) as result FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics n WHERE n.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(0.7853981633974483, $result[0]['result']);
    }

    #[Test]
    public function returns_the_arc_tangent_from_an_entity_field(): void
    {
        $dql = 'SELECT ATAN(n.decimal1) as result FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics n WHERE n.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(1.4758446204521403, $result[0]['result']);
    }
}
