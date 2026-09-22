<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Corr;
use PHPUnit\Framework\Attributes\Test;

final class CorrTest extends NumericTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'CORR' => Corr::class,
        ];
    }

    #[Test]
    public function returns_the_correlation_coefficient_from_entity_fields(): void
    {
        $dql = 'SELECT CORR(t.integer1, t.integer2) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics t';
        $result = $this->executeDqlQuery($dql);
        $this->assertNull($result[0]['result']);
    }
}
