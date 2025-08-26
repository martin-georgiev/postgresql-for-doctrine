<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonbPathMatch;
use PHPUnit\Framework\Attributes\Test;

class JsonbPathMatchTest extends JsonTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'JSONB_PATH_MATCH' => JsonbPathMatch::class,
        ];
    }

    #[Test]
    public function can_match_simple_path(): void
    {
        $dql = 'SELECT JSONB_PATH_MATCH(:json, :path) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsJsons t 
                WHERE t.id = 1';
        $result = $this->executeDqlQuery($dql, [
            'json' => '{"a": 1}',
            'path' => 'exists($.a)',
        ]);
        $this->assertTrue($result[0]['result']);
    }

    #[Test]
    public function can_match_comparison_expression(): void
    {
        $dql = 'SELECT JSONB_PATH_MATCH(:json, :path) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsJsons t 
                WHERE t.id = 1';
        $result = $this->executeDqlQuery($dql, [
            'json' => '{"a": 5}',
            'path' => '$.a > 3',
        ]);
        $this->assertTrue($result[0]['result']);
    }

    #[Test]
    public function returns_false_for_non_matching_path(): void
    {
        $dql = 'SELECT JSONB_PATH_MATCH(:json, :path) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsJsons t 
                WHERE t.id = 1';
        $result = $this->executeDqlQuery($dql, [
            'json' => '{"a": 1}',
            'path' => 'exists($.b)',
        ]);
        $this->assertFalse($result[0]['result']);
    }

    #[Test]
    public function can_match_with_column_reference(): void
    {
        $dql = 'SELECT JSONB_PATH_MATCH(t.jsonbObject1, :path) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsJsons t
                WHERE t.id = 1';
        $result = $this->executeDqlQuery($dql, ['path' => 'exists($.name)']);
        $this->assertTrue($result[0]['result']);
    }
}
