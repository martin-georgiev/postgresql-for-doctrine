<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonbPathMatch;

class JsonbPathMatchTest extends JsonTestCase
{
    protected function getStringFunctions(): array
    {
        return ['JSONB_PATH_MATCH' => JsonbPathMatch::class];
    }

    public function test_jsonb_path_match_simple(): void
    {
        $dql = "SELECT JSONB_PATH_MATCH('{\"a\": 1}', 'exists($.a)') as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsJsons t WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertTrue($result[0]['result']);
    }

    public function test_jsonb_path_match_comparison(): void
    {
        $dql = "SELECT JSONB_PATH_MATCH('{\"a\": 5}', '$.a > 3') as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsJsons t WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertTrue($result[0]['result']);
    }

    public function test_jsonb_path_match_negative(): void
    {
        $dql = "SELECT JSONB_PATH_MATCH('{\"a\": 1}', 'exists($.b)') as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsJsons t WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertFalse($result[0]['result']);
    }

    public function test_jsonb_path_match_with_column_reference(): void
    {
        $dql = 'SELECT JSONB_PATH_MATCH(t.object1, :path) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsJsons t 
                WHERE t.id = 1';
        $result = $this->executeDqlQuery($dql, ['path' => 'exists($.nested)']);
        $this->assertIsBool($result[0]['result']);
    }
}
