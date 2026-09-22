<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Tan;
use PHPUnit\Framework\Attributes\Test;

final class TanTest extends NumericTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'TAN' => Tan::class,
        ];
    }

    #[Test]
    public function returns_the_tangent_from_a_literal(): void
    {
        $dql = 'SELECT TAN(0.0) as result FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics n WHERE n.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(0.0, $result[0]['result']);
    }

    #[Test]
    public function returns_the_tangent_from_an_entity_field(): void
    {
        $dql = 'SELECT TAN(n.decimal1) as result FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics n WHERE n.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(1.8498999934219273, $result[0]['result']);
    }
}
