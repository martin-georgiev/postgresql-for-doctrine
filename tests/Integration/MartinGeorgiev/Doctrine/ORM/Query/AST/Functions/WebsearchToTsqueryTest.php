<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\WebsearchToTsquery;
use PHPUnit\Framework\Attributes\Test;

final class WebsearchToTsqueryTest extends TextTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'websearch_to_tsquery' => WebsearchToTsquery::class,
        ];
    }

    #[Test]
    public function creates_a_tsquery_with_a_text_search_config(): void
    {
        $dql = "SELECT websearch_to_tsquery('english', '\"sad cat\" or \"fat rat\"') as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertSame("'sad' <-> 'cat' | 'fat' <-> 'rat'", $result[0]['result']);
    }

    #[Test]
    public function creates_a_tsquery_from_a_literal(): void
    {
        $dql = "SELECT websearch_to_tsquery('\"sad cat\" or \"fat rat\"') as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertSame("'sad' <-> 'cat' | 'fat' <-> 'rat'", $result[0]['result']);
    }

    #[Test]
    public function creates_a_tsquery_from_an_entity_field(): void
    {
        $dql = 'SELECT websearch_to_tsquery(t.text1) as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t WHERE t.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertSame("'test' & 'string'", $result[0]['result']);
    }
}
