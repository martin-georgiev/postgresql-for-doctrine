<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Lgamma;
use PHPUnit\Framework\Attributes\Test;

class LgammaTest extends NumericTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->requirePostgresVersion(180000, 'lgamma function');
    }

    protected function getStringFunctions(): array
    {
        return [
            'LGAMMA' => Lgamma::class,
        ];
    }

    #[Test]
    public function can_compute_lgamma_of_a_integer(): void
    {
        $dql = 'SELECT LGAMMA(5) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics t
                WHERE t.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertEqualsWithDelta(3.178053830, $result[0]['result'], 0.0001);
    }

    #[Test]
    public function can_compute_lgamma_of_a_float(): void
    {
        $dql = 'SELECT LGAMMA(2.5) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics t
                WHERE t.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertEqualsWithDelta(0.284682870, $result[0]['result'], 0.0001);
    }

    #[Test]
    public function can_compute_lgamma_of_integer_field(): void
    {
        $dql = 'SELECT LGAMMA(t.integer1) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics t
                WHERE t.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertEqualsWithDelta(12.801827480, $result[0]['result'], 0.0001);
    }
}
