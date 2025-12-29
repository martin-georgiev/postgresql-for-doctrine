<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonbArrayLength;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonGetField;
use PHPUnit\Framework\Attributes\Test;

class JsonbArrayLengthTest extends JsonTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'JSONB_ARRAY_LENGTH' => JsonbArrayLength::class,
            'JSON_GET_FIELD' => JsonGetField::class,
        ];
    }

    #[Test]
    public function can_get_array_length_from_jsonb(): void
    {
        $dql = "SELECT JSONB_ARRAY_LENGTH(JSON_GET_FIELD(t.jsonbObject1, 'tags')) as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsJsons t
                WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertSame(2, $result[0]['result']);
    }

    #[Test]
    public function returns_zero_for_empty_array(): void
    {
        $dql = "SELECT JSONB_ARRAY_LENGTH(JSON_GET_FIELD(t.jsonbObject1, 'tags')) as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsJsons t
                WHERE t.id = 3";
        $result = $this->executeDqlQuery($dql);
        $this->assertSame(0, $result[0]['result']);
    }

    #[Test]
    public function can_get_length_of_single_element_array(): void
    {
        $dql = "SELECT JSONB_ARRAY_LENGTH(JSON_GET_FIELD(t.jsonbObject1, 'tags')) as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsJsons t
                WHERE t.id = 2";
        $result = $this->executeDqlQuery($dql);
        $this->assertSame(1, $result[0]['result']);
    }
}
