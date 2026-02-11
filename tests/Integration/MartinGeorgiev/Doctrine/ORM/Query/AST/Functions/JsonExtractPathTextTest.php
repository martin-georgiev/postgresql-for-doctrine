<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonExtractPathText;
use PHPUnit\Framework\Attributes\Test;

class JsonExtractPathTextTest extends JsonTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'JSON_EXTRACT_PATH_TEXT' => JsonExtractPathText::class,
        ];
    }

    #[Test]
    public function can_extract_path_text_from_json(): void
    {
        $dql = "SELECT JSON_EXTRACT_PATH_TEXT(t.jsonObject1, 'address', 'city') as result 
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsJsons t 
                WHERE t.id = 1";

        $result = $this->executeDqlQuery($dql);
        $this->assertIsString($result[0]['result']);
        $this->assertSame('New York', $result[0]['result']);
    }
}
