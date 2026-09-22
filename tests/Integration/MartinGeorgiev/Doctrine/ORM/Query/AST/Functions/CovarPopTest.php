<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\CovarPop;
use PHPUnit\Framework\Attributes\Test;

final class CovarPopTest extends NumericTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'COVAR_POP' => CovarPop::class,
        ];
    }

    #[Test]
    public function returns_the_population_covariance_from_entity_fields(): void
    {
        $dql = 'SELECT COVAR_POP(t.integer1, t.integer2) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics t';
        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(0.0, $result[0]['result']);
    }
}
