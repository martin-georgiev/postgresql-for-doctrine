<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ToTsquery;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ToTsvector;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Tsmatch;
use PHPUnit\Framework\Attributes\Test;

class ToTsqueryTest extends TextTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'TO_TSQUERY' => ToTsquery::class,
            'TO_TSVECTOR' => ToTsvector::class,
            'TSMATCH' => Tsmatch::class,
        ];
    }

    #[Test]
    public function can_create_tsquery_from_text(): void
    {
        $dql = "SELECT t.id as result 
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t 
                WHERE TSMATCH(TO_TSVECTOR(t.text1), TO_TSQUERY('test')) = true 
                AND t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertSame(1, $result[0]['result']);
    }

    #[Test]
    public function can_create_tsquery_with_language(): void
    {
        $dql = "SELECT t.id as result 
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t 
                WHERE TSMATCH(TO_TSVECTOR('english', t.text1), TO_TSQUERY('english', 'test')) = true 
                AND t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertSame(1, $result[0]['result']);
    }

    #[Test]
    public function returns_empty_when_no_match(): void
    {
        $dql = "SELECT t.id as result 
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t 
                WHERE TSMATCH(TO_TSVECTOR(t.text1), TO_TSQUERY('nonexistent')) = true";
        $result = $this->executeDqlQuery($dql);
        $this->assertCount(0, $result);
    }
}
