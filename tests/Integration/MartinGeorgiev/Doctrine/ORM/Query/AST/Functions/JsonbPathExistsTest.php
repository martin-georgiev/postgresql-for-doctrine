<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonbPathExists;
use PHPUnit\Framework\Attributes\Test;

final class JsonbPathExistsTest extends JsonTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'JSONB_PATH_EXISTS' => JsonbPathExists::class,
        ];
    }

    #[Test]
    public function returns_true_when_the_path_exists_in_a_json_literal(): void
    {
        $dql = 'SELECT JSONB_PATH_EXISTS(:json, :path) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsJsons t 
                WHERE t.id = 1';
        $result = $this->executeDqlQuery($dql, [
            'json' => '{"a": 1, "b": 2}',
            'path' => '$.b',
        ]);
        $this->assertTrue($result[0]['result']);
    }

    #[Test]
    public function returns_true_when_the_path_exists_in_an_entity_field(): void
    {
        $dql = 'SELECT JSONB_PATH_EXISTS(t.jsonbObject1, :path) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsJsons t
                WHERE t.id = 1';
        $result = $this->executeDqlQuery($dql, ['path' => '$.name']);
        $this->assertTrue($result[0]['result']);
    }
}
