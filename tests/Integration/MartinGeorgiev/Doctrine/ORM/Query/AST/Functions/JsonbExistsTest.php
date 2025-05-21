<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonbExists;

class JsonbExistsTest extends JsonTestCase
{
    protected function getStringFunctions(): array
    {
        return ['JSONB_EXISTS' => JsonbExists::class];
    }

    public function test_jsonb_exists_with_existing_key(): void
    {
        $dql = 'SELECT JSONB_EXISTS(t.object1, :key) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsJsons t 
                WHERE t.id = 1';

        $result = $this->executeDqlQuery($dql, ['key' => 'name']);
        $this->assertTrue($result[0]['result']);
    }

    public function test_jsonb_exists_with_nested_key(): void
    {
        $dql = 'SELECT JSONB_EXISTS(t.object1, :key) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsJsons t 
                WHERE t.id = 1';

        $result = $this->executeDqlQuery($dql, ['key' => 'address']);
        $this->assertTrue($result[0]['result']);
    }

    public function test_jsonb_exists_with_array_element(): void
    {
        $dql = 'SELECT JSONB_EXISTS(t.object1, :key) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsJsons t 
                WHERE t.id = 1';

        $result = $this->executeDqlQuery($dql, ['key' => 'tags']);
        $this->assertTrue($result[0]['result']);
    }

    public function test_jsonb_exists_with_non_existing_key(): void
    {
        $dql = 'SELECT JSONB_EXISTS(t.object1, :key) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsJsons t 
                WHERE t.id = 1';

        $result = $this->executeDqlQuery($dql, ['key' => 'non_existing']);
        $this->assertFalse($result[0]['result']);
    }
}
