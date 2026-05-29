<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsTexts;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\QuoteLiteral;

class QuoteLiteralTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'QUOTE_LITERAL' => QuoteLiteral::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'quotes a literal string value' => "SELECT quote_literal('it''s a test') AS sclr_0 FROM ContainsTexts c0_",
            'quotes a text field value' => 'SELECT quote_literal(c0_.text1) AS sclr_0 FROM ContainsTexts c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'quotes a literal string value' => \sprintf("SELECT QUOTE_LITERAL('it''s a test') FROM %s e", ContainsTexts::class),
            'quotes a text field value' => \sprintf('SELECT QUOTE_LITERAL(e.text1) FROM %s e', ContainsTexts::class),
        ];
    }
}
