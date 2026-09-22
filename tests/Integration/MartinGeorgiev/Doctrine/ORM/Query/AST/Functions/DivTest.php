<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Div;
use PHPUnit\Framework\Attributes\Test;

final class DivTest extends NumericTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'DIV' => Div::class,
        ];
    }

    #[Test]
    public function returns_the_integer_quotient_from_numeric_literals(): void
    {
        $dql = 'SELECT DIV(9, 4) as result FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics n WHERE n.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(2, $result[0]['result']);
    }

    #[Test]
    public function returns_the_integer_quotient_from_entity_fields(): void
    {
        $dql = 'SELECT DIV(n.integer1, n.integer2) as result FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics n WHERE n.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(0, $result[0]['result']);
    }
}
