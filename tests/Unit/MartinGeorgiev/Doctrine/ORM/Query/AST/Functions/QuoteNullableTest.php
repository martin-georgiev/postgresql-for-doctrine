<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsTexts;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\QuoteNullable;

class QuoteNullableTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'QUOTE_NULLABLE' => QuoteNullable::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'quotes a literal string value' => "SELECT quote_nullable('hello') AS sclr_0 FROM ContainsTexts c0_",
            'quotes a text field value' => 'SELECT quote_nullable(c0_.text1) AS sclr_0 FROM ContainsTexts c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'quotes a literal string value' => \sprintf("SELECT QUOTE_NULLABLE('hello') FROM %s e", ContainsTexts::class),
            'quotes a text field value' => \sprintf('SELECT QUOTE_NULLABLE(e.text1) FROM %s e', ContainsTexts::class),
        ];
    }
}
