<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonbPathQuery;
use PHPUnit\Framework\Attributes\Test;

final class JsonbPathQueryTest extends JsonTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'JSONB_PATH_QUERY' => JsonbPathQuery::class,
        ];
    }

    #[Test]
    public function returns_the_queried_values_from_a_json_literal(): void
    {
        $dql = 'SELECT JSONB_PATH_QUERY(:json, :path) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsJsons t 
                WHERE t.id = 1';
        $result = $this->executeDqlQuery($dql, [
            'json' => '{"a": 1, "b": 2}',
            'path' => '$.b',
        ]);
        $this->assertCount(1, $result);
        $this->assertSame('2', $result[0]['result']);
    }

    #[Test]
    public function returns_the_queried_values_from_an_entity_field(): void
    {
        $dql = 'SELECT JSONB_PATH_QUERY(t.jsonbObject1, :path) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsJsons t
                WHERE t.id = 1';
        $result = $this->executeDqlQuery($dql, ['path' => '$.tags[*]']);
        $this->assertCount(2, $result);
        $this->assertSame('"developer"', $result[0]['result']);
        $this->assertSame('"manager"', $result[1]['result']);
    }
}
