<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\DeleteAtPath;
use PHPUnit\Framework\Attributes\Test;

final class DeleteAtPathTest extends JsonTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'DELETE_AT_PATH' => DeleteAtPath::class,
        ];
    }

    #[Test]
    public function returns_the_reduced_json_from_a_json_literal(): void
    {
        $dql = 'SELECT DELETE_AT_PATH(:json, :path) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsJsons t 
                WHERE t.id = 1';
        $result = $this->executeDqlQuery($dql, [
            'json' => '{"a": {"b": {"c": "value"}}}',
            'path' => '{a,b}',
        ]);
        $this->assertSame('{"a": {}}', $result[0]['result']);
    }

    #[Test]
    public function returns_the_reduced_json_from_an_entity_field(): void
    {
        $dql = 'SELECT DELETE_AT_PATH(t.jsonbObject1, :path) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsJsons t
                WHERE t.id = 1';
        $result = $this->executeDqlQuery($dql, ['path' => '{address,city}']);
        $this->assertSame('{"age": 30, "name": "John", "tags": ["developer", "manager"], "address": {}}', $result[0]['result']);
    }
}
