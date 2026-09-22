<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonExtractPath;
use PHPUnit\Framework\Attributes\Test;

final class JsonExtractPathTest extends JsonTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'JSON_EXTRACT_PATH' => JsonExtractPath::class,
        ];
    }

    #[Test]
    public function returns_the_json_at_a_path_from_an_entity_field(): void
    {
        $dql = "SELECT JSON_EXTRACT_PATH(t.jsonObject1, 'address', 'city') as result 
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsJsons t 
                WHERE t.id = 1";

        $result = $this->executeDqlQuery($dql);
        $this->assertSame('"New York"', $result[0]['result']);
    }
}
