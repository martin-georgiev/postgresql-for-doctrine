<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Ln;
use PHPUnit\Framework\Attributes\Test;

final class LnTest extends NumericTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'LN' => Ln::class,
        ];
    }

    #[Test]
    public function returns_the_natural_logarithm_from_a_numeric_literal(): void
    {
        $dql = 'SELECT LN(2.7182818284590452) as result FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics t WHERE t.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(1.0, $result[0]['result']);
    }

    #[Test]
    public function returns_the_natural_logarithm_from_an_entity_field(): void
    {
        $dql = 'SELECT LN(n.decimal1) as result FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics n WHERE n.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(2.3513752571634777, $result[0]['result']);
    }
}
