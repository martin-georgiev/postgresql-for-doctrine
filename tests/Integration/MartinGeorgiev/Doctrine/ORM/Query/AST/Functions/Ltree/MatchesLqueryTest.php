<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Ltree;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Ltree\MatchesLquery;
use PHPUnit\Framework\Attributes\Test;

final class MatchesLqueryTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'MATCHES_LQUERY' => MatchesLquery::class,
        ];
    }

    #[Test]
    public function returns_whether_the_pattern_matches_from_an_entity_field(): void
    {
        $dql = "SELECT l.id FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsLtrees l
                WHERE MATCHES_LQUERY(l.ltree1, 'Top.*') = TRUE AND l.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertCount(1, $result);
    }
}
