<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ToTsquery;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\TsqueryPhrase;
use PHPUnit\Framework\Attributes\Test;

final class TsqueryPhraseTest extends TextTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'TO_TSQUERY' => ToTsquery::class,
            'TSQUERY_PHRASE' => TsqueryPhrase::class,
        ];
    }

    #[Test]
    public function creates_a_phrase_query_from_two_tsqueries(): void
    {
        $dql = "SELECT TSQUERY_PHRASE(TO_TSQUERY('lorem'), TO_TSQUERY('ipsum')) as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertSame("'lorem' <-> 'ipsum'", $result[0]['result']);
    }

    #[Test]
    public function creates_a_phrase_query_with_a_distance(): void
    {
        $dql = "SELECT TSQUERY_PHRASE(TO_TSQUERY('lorem'), TO_TSQUERY('ipsum'), 2) as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertSame("'lorem' <2> 'ipsum'", $result[0]['result']);
    }
}
