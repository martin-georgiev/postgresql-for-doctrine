<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonGetField;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonTypeof;
use PHPUnit\Framework\Attributes\Test;

class JsonTypeofTest extends JsonTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'JSON_TYPEOF' => JsonTypeof::class,
            'JSON_GET_FIELD' => JsonGetField::class,
        ];
    }

    #[Test]
    public function can_detect_object_type(): void
    {
        $dql = 'SELECT JSON_TYPEOF(t.jsonObject1) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsJsons t
                WHERE t.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertSame('object', $result[0]['result']);
    }

    #[Test]
    public function can_detect_array_type(): void
    {
        $dql = "SELECT JSON_TYPEOF(JSON_GET_FIELD(t.jsonObject1, 'tags')) as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsJsons t
                WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertSame('array', $result[0]['result']);
    }

    #[Test]
    public function can_detect_string_type(): void
    {
        $dql = "SELECT JSON_TYPEOF(JSON_GET_FIELD(t.jsonObject1, 'name')) as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsJsons t
                WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertSame('string', $result[0]['result']);
    }

    #[Test]
    public function can_detect_number_type(): void
    {
        $dql = "SELECT JSON_TYPEOF(JSON_GET_FIELD(t.jsonObject1, 'age')) as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsJsons t
                WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertSame('number', $result[0]['result']);
    }
}
