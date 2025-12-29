<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ToTsquery;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ToTsvector;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Tsmatch;
use PHPUnit\Framework\Attributes\Test;

class ToTsvectorTest extends TextTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'TO_TSVECTOR' => ToTsvector::class,
            'TO_TSQUERY' => ToTsquery::class,
            'TSMATCH' => Tsmatch::class,
        ];
    }

    #[Test]
    public function can_create_tsvector_from_text(): void
    {
        $dql = "SELECT t.id as result 
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t 
                WHERE TSMATCH(TO_TSVECTOR(t.text1), TO_TSQUERY('lorem')) = true 
                AND t.id = 2";
        $result = $this->executeDqlQuery($dql);
        $this->assertCount(1, $result);
    }

    #[Test]
    public function can_create_tsvector_with_language(): void
    {
        $dql = "SELECT t.id as result 
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t 
                WHERE TSMATCH(TO_TSVECTOR('english', t.text1), TO_TSQUERY('english', 'lorem')) = true 
                AND t.id = 2";
        $result = $this->executeDqlQuery($dql);
        $this->assertCount(1, $result);
    }
}
