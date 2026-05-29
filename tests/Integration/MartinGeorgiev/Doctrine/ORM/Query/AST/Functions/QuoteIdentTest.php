<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\QuoteIdent;
use PHPUnit\Framework\Attributes\Test;

class QuoteIdentTest extends TextTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'QUOTE_IDENT' => QuoteIdent::class,
        ];
    }

    #[Test]
    public function returns_quoted_sql_identifier_from_literal(): void
    {
        $dql = "SELECT QUOTE_IDENT('Hello World') as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t
                WHERE t.id = 1";

        $result = $this->executeDqlQuery($dql);
        $this->assertSame('"Hello World"', $result[0]['result']);
    }

    #[Test]
    public function returns_quoted_sql_identifier_from_text_field(): void
    {
        $dql = 'SELECT QUOTE_IDENT(t.text1) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsTexts t
                WHERE t.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertSame('"this is a test string"', $result[0]['result']);
    }
}
