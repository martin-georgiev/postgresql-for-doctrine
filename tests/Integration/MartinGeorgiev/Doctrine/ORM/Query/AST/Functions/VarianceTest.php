<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Variance;
use PHPUnit\Framework\Attributes\Test;

final class VarianceTest extends NumericTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'VARIANCE' => Variance::class,
        ];
    }

    #[Test]
    public function returns_the_sample_variance_from_an_entity_field(): void
    {
        $dql = 'SELECT VARIANCE(t.integer1) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics t';
        $result = $this->executeDqlQuery($dql);
        $this->assertNull($result[0]['result']);
    }
}
