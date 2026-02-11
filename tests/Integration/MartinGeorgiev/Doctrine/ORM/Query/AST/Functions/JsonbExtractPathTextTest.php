<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonbExtractPathText;
use PHPUnit\Framework\Attributes\Test;

class JsonbExtractPathTextTest extends JsonTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'JSONB_EXTRACT_PATH_TEXT' => JsonbExtractPathText::class,
        ];
    }

    #[Test]
    public function can_extract_path_text_from_jsonb(): void
    {
        $dql = "SELECT JSONB_EXTRACT_PATH_TEXT(t.jsonbObject1, 'address', 'city') as result 
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsJsons t 
                WHERE t.id = 1";

        $result = $this->executeDqlQuery($dql);
        $this->assertIsString($result[0]['result']);
        $this->assertSame('New York', $result[0]['result']);
    }
}
