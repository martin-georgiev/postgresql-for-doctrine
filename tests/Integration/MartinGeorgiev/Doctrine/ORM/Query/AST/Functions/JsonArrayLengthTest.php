<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonArrayLength;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonGetField;
use PHPUnit\Framework\Attributes\Test;

final class JsonArrayLengthTest extends JsonTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'JSON_ARRAY_LENGTH' => JsonArrayLength::class,
            'JSON_GET_FIELD' => JsonGetField::class,
        ];
    }

    #[Test]
    public function returns_the_json_array_length_from_an_entity_field(): void
    {
        $dql = "SELECT JSON_ARRAY_LENGTH(JSON_GET_FIELD(t.jsonObject1, 'tags')) as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsJsons t
                WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertSame(2, $result[0]['result']);
    }
}
