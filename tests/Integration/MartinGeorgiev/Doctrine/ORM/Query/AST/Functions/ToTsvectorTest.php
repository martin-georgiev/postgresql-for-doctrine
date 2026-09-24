<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ToTsvector;
use PHPUnit\Framework\Attributes\Test;

final class ToTsvectorTest extends TextTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'TO_TSVECTOR' => ToTsvector::class,
        ];
    }

    #[Test]
    public function creates_a_tsvector_from_a_literal(): void
    {
        $dql = "SELECT TO_TSVECTOR('lorem ipsum dolor') as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t
                WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertSame("'dolor':3 'ipsum':2 'lorem':1", $result[0]['result']);
    }

    #[Test]
    public function creates_a_tsvector_from_an_entity_field(): void
    {
        $dql = 'SELECT TO_TSVECTOR(t.text1) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsTexts t
                WHERE t.id = 2';
        $result = $this->executeDqlQuery($dql);
        $this->assertSame("'dolor':3 'ipsum':2 'lorem':1", $result[0]['result']);
    }

    #[Test]
    public function creates_a_tsvector_with_a_text_search_config(): void
    {
        $dql = "SELECT TO_TSVECTOR('english', t.text1) as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t
                WHERE t.id = 2";
        $result = $this->executeDqlQuery($dql);
        $this->assertSame("'dolor':3 'ipsum':2 'lorem':1", $result[0]['result']);
    }
}
