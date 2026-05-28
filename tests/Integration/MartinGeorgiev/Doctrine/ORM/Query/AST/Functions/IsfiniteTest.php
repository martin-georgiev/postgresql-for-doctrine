<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Isfinite;
use PHPUnit\Framework\Attributes\Test;

class IsfiniteTest extends DateTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ISFINITE' => Isfinite::class,
        ];
    }

    #[Test]
    public function returns_true_for_finite_date(): void
    {
        $dql = 'SELECT ISFINITE(t.date1) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsDates t
                WHERE t.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertTrue($result[0]['result']);
    }

    #[Test]
    public function returns_true_for_finite_interval(): void
    {
        $dql = 'SELECT ISFINITE(t.dateinterval1) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsDates t
                WHERE t.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertTrue($result[0]['result']);
    }
}
