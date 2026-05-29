<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsTexts;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\QuoteIdent;

class QuoteIdentTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'QUOTE_IDENT' => QuoteIdent::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'quotes a literal identifier string' => "SELECT quote_ident('my_table') AS sclr_0 FROM ContainsTexts c0_",
            'quotes an identifier from a text field' => 'SELECT quote_ident(c0_.text1) AS sclr_0 FROM ContainsTexts c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'quotes a literal identifier string' => \sprintf("SELECT QUOTE_IDENT('my_table') FROM %s e", ContainsTexts::class),
            'quotes an identifier from a text field' => \sprintf('SELECT QUOTE_IDENT(e.text1) FROM %s e', ContainsTexts::class),
        ];
    }
}
