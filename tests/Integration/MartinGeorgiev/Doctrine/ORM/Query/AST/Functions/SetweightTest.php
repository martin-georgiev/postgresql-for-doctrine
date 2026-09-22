<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Setweight;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ToTsvector;
use PHPUnit\Framework\Attributes\Test;

final class SetweightTest extends TextTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'TO_TSVECTOR' => ToTsvector::class,
            'SETWEIGHT' => Setweight::class,
        ];
    }

    #[Test]
    public function returns_a_weighted_tsvector_from_an_entity_field(): void
    {
        $dql = "SELECT SETWEIGHT(TO_TSVECTOR(t.text1), 'A') as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t WHERE t.id = 2";
        $result = $this->executeDqlQuery($dql);
        $this->assertSame("'dolor':3A 'ipsum':2A 'lorem':1A", $result[0]['result']);
    }

    #[Test]
    public function returns_a_weighted_tsvector_from_a_literal(): void
    {
        $dql = "SELECT SETWEIGHT(TO_TSVECTOR('lorem ipsum dolor'), 'B') as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertSame("'dolor':3B 'ipsum':2B 'lorem':1B", $result[0]['result']);
    }
}
