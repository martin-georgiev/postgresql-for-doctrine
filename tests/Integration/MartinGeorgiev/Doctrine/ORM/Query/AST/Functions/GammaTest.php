<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Gamma;
use PHPUnit\Framework\Attributes\Test;

final class GammaTest extends NumericTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->requirePostgresVersion(180000, 'gamma function');
    }

    protected function getStringFunctions(): array
    {
        return [
            'GAMMA' => Gamma::class,
        ];
    }

    #[Test]
    public function returns_the_gamma_from_a_numeric_literal(): void
    {
        $dql = 'SELECT GAMMA(5) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics t
                WHERE t.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(24.0, $result[0]['result']);
    }

    #[Test]
    public function returns_the_gamma_from_an_entity_field(): void
    {
        $dql = 'SELECT GAMMA(t.integer1) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics t
                WHERE t.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(362880.0, $result[0]['result']);
    }
}
