<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PhrasetoTsquery;
use PHPUnit\Framework\Attributes\Test;

class PhrasetoTsqueryTest extends TextTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'PHRASETO_TSQUERY' => PhrasetoTsquery::class,
        ];
    }

    #[Test]
    public function can_convert_phrase_to_tsquery(): void
    {
        $dql = "SELECT PHRASETO_TSQUERY('morum ipsum') as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertSame("'morum' <-> 'ipsum'", $result[0]['result']);
    }

    #[Test]
    public function can_convert_phrase_with_config(): void
    {
        $dql = "SELECT PHRASETO_TSQUERY('english', 'lorem ipsum') as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertSame("'lorem' <-> 'ipsum'", $result[0]['result']);
    }

    #[Test]
    public function can_convert_field_value_to_phrase_tsquery(): void
    {
        $dql = 'SELECT PHRASETO_TSQUERY(t.text2) as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t WHERE t.id = 2';
        $result = $this->executeDqlQuery($dql);
        $this->assertSame("'sit' <-> 'amet'", $result[0]['result']);
    }
}
