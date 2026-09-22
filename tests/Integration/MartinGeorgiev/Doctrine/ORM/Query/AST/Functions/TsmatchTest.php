<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ToTsquery;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ToTsvector;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Tsmatch;
use PHPUnit\Framework\Attributes\Test;

final class TsmatchTest extends TextTestCase
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
    public function returns_true_when_an_entity_field_matches_the_query(): void
    {
        $dql = "SELECT TSMATCH(TO_TSVECTOR(t.text1), TO_TSQUERY('test')) as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertTrue($result[0]['result']);
    }

    #[Test]
    public function returns_false_when_a_literal_does_not_match_the_query(): void
    {
        $dql = "SELECT TSMATCH(TO_TSVECTOR('this is a test string'), TO_TSQUERY('nonexistent')) as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertFalse($result[0]['result']);
    }
}
