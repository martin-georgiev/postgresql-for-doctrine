<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\QuoteLiteral;
use PHPUnit\Framework\Attributes\Test;

final class QuoteLiteralTest extends TextTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'QUOTE_LITERAL' => QuoteLiteral::class,
        ];
    }

    #[Test]
    public function returns_the_quoted_literal_from_a_literal(): void
    {
        $dql = "SELECT QUOTE_LITERAL('hello') as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t
                WHERE t.id = 1";

        $result = $this->executeDqlQuery($dql);
        $this->assertSame("'hello'", $result[0]['result']);
    }

    #[Test]
    public function returns_the_quoted_literal_from_an_entity_field(): void
    {
        $dql = 'SELECT QUOTE_LITERAL(t.text2) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsTexts t
                WHERE t.id = 3';

        $result = $this->executeDqlQuery($dql);
        $this->assertSame("'bar'", $result[0]['result']);
    }
}
