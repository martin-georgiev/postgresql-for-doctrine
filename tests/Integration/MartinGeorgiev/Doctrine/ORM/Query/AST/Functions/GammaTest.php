<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Gamma;
use PHPUnit\Framework\Attributes\Test;

class GammaTest extends NumericTestCase
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
    public function can_compute_gamma_of_a_integer(): void
    {
        $dql = 'SELECT GAMMA(5) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics t
                WHERE t.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertIsFloat($result[0]['result']);
        $this->assertEqualsWithDelta(24.0, $result[0]['result'], 0.0001);
    }

    #[Test]
    public function can_compute_gamma_of_a_float(): void
    {
        $dql = 'SELECT GAMMA(2.5) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics t
                WHERE t.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertIsFloat($result[0]['result']);
        $this->assertEqualsWithDelta(1.329340388, $result[0]['result'], 0.0001);
    }

    #[Test]
    public function can_compute_gamma_of_integer_field(): void
    {
        $dql = 'SELECT GAMMA(t.integer1) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics t
                WHERE t.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertIsFloat($result[0]['result']);
        $this->assertEqualsWithDelta(362880.0, $result[0]['result'], 0.0001);
    }
}
