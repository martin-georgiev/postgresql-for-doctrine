<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonbTypeof;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonGetField;
use PHPUnit\Framework\Attributes\Test;

class JsonbTypeofTest extends JsonTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'JSONB_TYPEOF' => JsonbTypeof::class,
            'JSON_GET_FIELD' => JsonGetField::class,
        ];
    }

    #[Test]
    public function can_get_type_of_jsonb_object(): void
    {
        $dql = 'SELECT JSONB_TYPEOF(t.jsonbObject1) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsJsons t 
                WHERE t.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertIsString($result[0]['result']);
        $this->assertSame('object', $result[0]['result']);
    }

    #[Test]
    public function can_get_type_of_jsonb_array(): void
    {
        $dql = "SELECT JSONB_TYPEOF(JSON_GET_FIELD(t.jsonbObject1, 'tags')) as result 
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsJsons t 
                WHERE t.id = 1";

        $result = $this->executeDqlQuery($dql);
        $this->assertIsString($result[0]['result']);
        $this->assertSame('array', $result[0]['result']);
    }
}
