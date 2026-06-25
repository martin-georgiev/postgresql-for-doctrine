<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Cosh;
use PHPUnit\Framework\Attributes\Test;

final class CoshTest extends NumericTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'COSH' => Cosh::class,
        ];
    }

    #[Test]
    public function calculates_cosh_of_literal(): void
    {
        $dql = 'SELECT COSH(0.0) as result FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics n WHERE n.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(1.0, $result[0]['result']);
    }

    #[Test]
    public function calculates_cosh_with_entity_property(): void
    {
        $dql = 'SELECT COSH(n.decimal1) as result FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics n WHERE n.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(18157.751350891544, $result[0]['result']);
    }
}
