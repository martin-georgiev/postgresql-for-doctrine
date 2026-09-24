<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Ltree;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Arr;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Ltree\MatchesAnyLquery;
use PHPUnit\Framework\Attributes\Test;

final class MatchesAnyLqueryTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'MATCHES_ANY_LQUERY' => MatchesAnyLquery::class,
            'ARR' => Arr::class,
        ];
    }

    #[Test]
    public function returns_whether_any_pattern_matches_from_an_entity_field(): void
    {
        $dql = "SELECT l.id FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsLtrees l
                WHERE MATCHES_ANY_LQUERY(l.ltree1, ARR('Root.*', 'Top.*')) = TRUE AND l.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertCount(1, $result);
    }
}
