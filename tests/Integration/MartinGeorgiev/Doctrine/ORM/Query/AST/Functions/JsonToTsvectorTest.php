<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonToTsvector;
use PHPUnit\Framework\Attributes\Test;

final class JsonToTsvectorTest extends JsonTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'JSON_TO_TSVECTOR' => JsonToTsvector::class,
        ];
    }

    #[Test]
    public function converts_the_json_to_a_tsvector_from_an_entity_field(): void
    {
        $dql = "SELECT JSON_TO_TSVECTOR(t.jsonObject1, '[\"string\"]') as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsJsons t WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertSame("'develop':3 'john':1 'manag':5 'new':7 'york':8", $result[0]['result']);
    }

    #[Test]
    public function converts_an_entity_field_to_a_tsvector_with_a_text_search_config(): void
    {
        $dql = "SELECT JSON_TO_TSVECTOR('english', t.jsonObject1, '[\"string\"]') as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsJsons t WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertSame("'develop':3 'john':1 'manag':5 'new':7 'york':8", $result[0]['result']);
    }
}
