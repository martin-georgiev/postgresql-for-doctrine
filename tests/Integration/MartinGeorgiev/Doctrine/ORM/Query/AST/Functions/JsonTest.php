<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Json;
use PHPUnit\Framework\Attributes\Test;

final class JsonTest extends JsonTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->requirePostgresVersion(170000, 'JSON function');
    }

    protected function getStringFunctions(): array
    {
        return [
            'JSON' => Json::class,
        ];
    }

    #[Test]
    public function returns_the_parsed_json_from_a_text_literal(): void
    {
        $dql = "SELECT JSON('{\"a\": 1}') as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsJsons t
                WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertSame('{"a": 1}', $result[0]['result']);
    }

    #[Test]
    public function returns_the_parsed_json_from_an_entity_field(): void
    {
        $dql = 'SELECT JSON(t.jsonObject1) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsJsons t
                WHERE t.id = 4';
        $result = $this->executeDqlQuery($dql);
        $this->assertSame('{}', $result[0]['result']);
    }

    #[Test]
    public function returns_the_parsed_json_with_unique_keys(): void
    {
        $dql = "SELECT JSON('{\"a\": 1}' WITH UNIQUE KEYS) as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsJsons t
                WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertSame('{"a": 1}', $result[0]['result']);
    }
}
