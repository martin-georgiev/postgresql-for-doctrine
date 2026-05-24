<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\QuoteNullable;
use PHPUnit\Framework\Attributes\Test;

class QuoteNullableTest extends TextTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'QUOTE_NULLABLE' => QuoteNullable::class,
        ];
    }

    #[Test]
    public function can_quote_a_literal_string_as_sql_literal(): void
    {
        $dql = "SELECT QUOTE_NULLABLE('hello') as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t
                WHERE t.id = 1";

        $result = $this->executeDqlQuery($dql);
        $this->assertSame("'hello'", $result[0]['result']);
    }

    #[Test]
    public function can_quote_text_field_as_null_safe_sql_literal(): void
    {
        $dql = 'SELECT QUOTE_NULLABLE(t.text2) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsTexts t
                WHERE t.id = 3';

        $result = $this->executeDqlQuery($dql);
        $this->assertSame("'bar'", $result[0]['result']);
    }
}
