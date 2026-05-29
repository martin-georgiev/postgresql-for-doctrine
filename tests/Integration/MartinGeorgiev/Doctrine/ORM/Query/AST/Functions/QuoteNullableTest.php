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
    public function returns_quoted_sql_literal_for_non_null_value(): void
    {
        $dql = "SELECT QUOTE_NULLABLE('hello') as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t
                WHERE t.id = 1";

        $result = $this->executeDqlQuery($dql);
        $this->assertSame("'hello'", $result[0]['result']);
    }

    #[Test]
    public function returns_quoted_sql_literal_from_text_field(): void
    {
        $dql = 'SELECT QUOTE_NULLABLE(t.text2) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsTexts t
                WHERE t.id = 3';

        $result = $this->executeDqlQuery($dql);
        $this->assertSame("'bar'", $result[0]['result']);
    }
}
