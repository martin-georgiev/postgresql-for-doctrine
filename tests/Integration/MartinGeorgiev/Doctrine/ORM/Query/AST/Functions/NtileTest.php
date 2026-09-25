<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Ntile;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Over;
use PHPUnit\Framework\Attributes\Test;

final class NtileTest extends NumericTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'NTILE' => Ntile::class,
            'OVER' => Over::class,
        ];
    }

    #[Test]
    public function returns_the_bucket_from_a_numeric_literal(): void
    {
        $dql = 'SELECT OVER(NTILE(4), ORDER BY n.integer2) as result FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics n WHERE n.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertSame(1, $result[0]['result']);
    }

    #[Test]
    public function returns_the_bucket_from_entity_fields(): void
    {
        $dql = 'SELECT OVER(NTILE(n.integer1), PARTITION BY n.integer1 ORDER BY n.integer2) as result FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics n WHERE n.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertSame(1, $result[0]['result']);
    }
}
