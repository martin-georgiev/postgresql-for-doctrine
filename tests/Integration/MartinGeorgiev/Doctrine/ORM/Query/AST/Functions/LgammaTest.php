<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Lgamma;
use PHPUnit\Framework\Attributes\Test;

final class LgammaTest extends NumericTestCase
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
    public function computes_lgamma_of_a_integer(): void
    {
        $dql = 'SELECT LGAMMA(5) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics t
                WHERE t.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(3.1780538303479458, $result[0]['result']);
    }

    #[Test]
    public function computes_lgamma_of_a_float(): void
    {
        $dql = 'SELECT LGAMMA(2.5) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics t
                WHERE t.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(0.2846828704729192, $result[0]['result']);
    }

    #[Test]
    public function computes_lgamma_of_integer_field(): void
    {
        $dql = 'SELECT LGAMMA(t.integer1) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics t
                WHERE t.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(12.80182748008147, $result[0]['result']);
    }
}
