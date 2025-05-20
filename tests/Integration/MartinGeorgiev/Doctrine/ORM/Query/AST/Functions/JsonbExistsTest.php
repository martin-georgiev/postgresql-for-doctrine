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
        $dql = "SELECT JSONB_EXISTS(t.jsonb_data, 'name') as result 
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsJsons t 
                WHERE t.id = 1";

        $result = $this->executeDqlQuery($dql);
        $this->assertTrue($result[0]['result']);
    }

    public function test_jsonb_exists_with_nested_key(): void
    {
        $dql = "SELECT JSONB_EXISTS(t.jsonb_data, 'address') as result 
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsJsons t 
                WHERE t.id = 1";

        $result = $this->executeDqlQuery($dql);
        $this->assertTrue($result[0]['result']);
    }

    public function test_jsonb_exists_with_array_element(): void
    {
        $dql = "SELECT JSONB_EXISTS(t.jsonb_data, 'tags') as result 
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsJsons t 
                WHERE t.id = 1";

        $result = $this->executeDqlQuery($dql);
        $this->assertTrue($result[0]['result']);
    }

    public function test_jsonb_exists_with_non_existing_key(): void
    {
        $dql = "SELECT JSONB_EXISTS(t.jsonb_data, 'non_existing') as result 
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsJsons t 
                WHERE t.id = 1";

        $result = $this->executeDqlQuery($dql);
        $this->assertFalse($result[0]['result']);
    }
}
