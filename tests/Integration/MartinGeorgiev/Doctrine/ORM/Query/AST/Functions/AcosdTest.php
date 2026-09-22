<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Acosd;
use PHPUnit\Framework\Attributes\Test;

final class AcosdTest extends NumericTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ACOSD' => Acosd::class,
        ];
    }

    #[Test]
    public function returns_the_arc_cosine_in_degrees_from_a_numeric_literal(): void
    {
        $dql = 'SELECT ACOSD(1.0) as result FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics n WHERE n.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(0.0, $result[0]['result']);
    }

    #[Test]
    public function returns_the_arc_cosine_in_degrees_from_an_entity_field(): void
    {
        $dql = 'SELECT ACOSD(n.decimal2 / 100.0) as result FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics n WHERE n.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(78.1705009513206, $result[0]['result']);
    }
}
