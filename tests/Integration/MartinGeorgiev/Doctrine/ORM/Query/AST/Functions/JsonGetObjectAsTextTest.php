<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonGetObjectAsText;
use PHPUnit\Framework\Attributes\Test;

final class JsonGetObjectAsTextTest extends JsonTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'JSON_GET_OBJECT_AS_TEXT' => JsonGetObjectAsText::class,
        ];
    }

    #[Test]
    public function returns_the_object_at_a_path_as_text_from_an_entity_field(): void
    {
        $dql = "SELECT JSON_GET_OBJECT_AS_TEXT(t.jsonObject1, '{address,city}') as result 
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsJsons t 
                WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertSame('New York', $result[0]['result']);
    }
}
