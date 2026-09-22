<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\StddevPop;
use PHPUnit\Framework\Attributes\Test;

final class StddevPopTest extends NumericTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'STDDEV_POP' => StddevPop::class,
        ];
    }

    #[Test]
    public function returns_the_population_standard_deviation_from_an_entity_field(): void
    {
        $dql = 'SELECT STDDEV_POP(t.integer1) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics t';
        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(0.0, $result[0]['result']);
    }
}
