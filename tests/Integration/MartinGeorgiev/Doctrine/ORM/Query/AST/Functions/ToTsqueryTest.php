<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ToTsquery;
use PHPUnit\Framework\Attributes\Test;

final class ToTsqueryTest extends TextTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'TO_TSQUERY' => ToTsquery::class,
        ];
    }

    #[Test]
    public function creates_a_tsquery_from_a_literal(): void
    {
        $dql = "SELECT TO_TSQUERY('test') as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t
                WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertSame("'test'", $result[0]['result']);
    }

    #[Test]
    public function creates_a_tsquery_from_an_entity_field(): void
    {
        $dql = 'SELECT TO_TSQUERY(t.text1) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsTexts t
                WHERE t.id = 3';
        $result = $this->executeDqlQuery($dql);
        $this->assertSame("'foo'", $result[0]['result']);
    }

    #[Test]
    public function creates_a_tsquery_with_a_config_argument(): void
    {
        $dql = "SELECT TO_TSQUERY('english', 'test') as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t
                WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertSame("'test'", $result[0]['result']);
    }
}
