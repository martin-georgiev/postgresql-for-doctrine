<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\QuoteLiteral;
use PHPUnit\Framework\Attributes\Test;

class QuoteLiteralTest extends TextTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'QUOTE_LITERAL' => QuoteLiteral::class,
        ];
    }

    #[Test]
    public function can_quote_a_literal_string_as_sql_literal(): void
    {
        $dql = "SELECT QUOTE_LITERAL('hello') as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t
                WHERE t.id = 1";

        $result = $this->executeDqlQuery($dql);
        $this->assertSame("'hello'", $result[0]['result']);
    }

    #[Test]
    public function can_quote_text_field_as_sql_literal(): void
    {
        $dql = 'SELECT QUOTE_LITERAL(t.text2) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsTexts t
                WHERE t.id = 3';

        $result = $this->executeDqlQuery($dql);
        $this->assertSame("'bar'", $result[0]['result']);
    }
}
