<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\CovarSamp;
use PHPUnit\Framework\Attributes\Test;

final class CovarSampTest extends NumericTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'COVAR_SAMP' => CovarSamp::class,
        ];
    }

    #[Test]
    public function returns_the_sample_covariance_from_entity_fields(): void
    {
        $dql = 'SELECT COVAR_SAMP(t.integer1, t.integer2) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics t';
        $result = $this->executeDqlQuery($dql);
        $this->assertNull($result[0]['result']);
    }
}
