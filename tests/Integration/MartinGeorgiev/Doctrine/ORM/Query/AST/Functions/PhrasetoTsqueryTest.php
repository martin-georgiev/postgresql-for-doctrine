<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PhrasetoTsquery;
use PHPUnit\Framework\Attributes\Test;

final class PhrasetoTsqueryTest extends TextTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'PHRASETO_TSQUERY' => PhrasetoTsquery::class,
        ];
    }

    #[Test]
    public function converts_the_text_to_a_tsquery_from_a_literal(): void
    {
        $dql = "SELECT PHRASETO_TSQUERY('morum ipsum') as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertSame("'morum' <-> 'ipsum'", $result[0]['result']);
    }

    #[Test]
    public function converts_a_literal_to_a_tsquery_with_a_config_argument(): void
    {
        $dql = "SELECT PHRASETO_TSQUERY('english', 'lorem ipsum') as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertSame("'lorem' <-> 'ipsum'", $result[0]['result']);
    }

    #[Test]
    public function converts_the_text_to_a_tsquery_from_an_entity_field(): void
    {
        $dql = 'SELECT PHRASETO_TSQUERY(t.text2) as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t WHERE t.id = 2';
        $result = $this->executeDqlQuery($dql);
        $this->assertSame("'sit' <-> 'amet'", $result[0]['result']);
    }
}
