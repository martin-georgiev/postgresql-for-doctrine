<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonbPretty;
use PHPUnit\Framework\Attributes\Test;

final class JsonbPrettyTest extends JsonTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'JSONB_PRETTY' => JsonbPretty::class,
        ];
    }

    #[Test]
    public function returns_the_pretty_printed_jsonb_from_an_entity_field(): void
    {
        $dql = 'SELECT JSONB_PRETTY(t.jsonbObject1) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsJsons t 
                WHERE t.id = 1';
        $result = $this->executeDqlQuery($dql);
        $expected = <<<'JSON'
            {
                "age": 30,
                "name": "John",
                "tags": [
                    "developer",
                    "manager"
                ],
                "address": {
                    "city": "New York"
                }
            }
            JSON;
        $this->assertSame($expected, $result[0]['result']);
    }
}
