<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonbToTsvector;
use PHPUnit\Framework\Attributes\Test;

final class JsonbToTsvectorTest extends JsonTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'JSONB_TO_TSVECTOR' => JsonbToTsvector::class,
        ];
    }

    #[Test]
    public function converts_the_jsonb_to_a_tsvector_from_an_entity_field(): void
    {
        $dql = "SELECT JSONB_TO_TSVECTOR('english', t.jsonbObject1, '[\"string\"]') as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsJsons t
                WHERE t.id = 1";

        $result = $this->executeDqlQuery($dql);
        $this->assertSame("'develop':3 'john':1 'manag':5 'new':7 'york':8", $result[0]['result']);
    }
}
