<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonbToTsvector;
use PHPUnit\Framework\Attributes\Test;

class JsonbToTsvectorTest extends JsonTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'JSONB_TO_TSVECTOR' => JsonbToTsvector::class,
        ];
    }

    #[Test]
    public function can_convert_jsonb_to_tsvector(): void
    {
        $dql = "SELECT JSONB_TO_TSVECTOR('english', t.jsonbObject1, '[\"string\"]') as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsJsons t
                WHERE t.id = 1";

        $result = $this->executeDqlQuery($dql);
        $this->assertIsString($result[0]['result']);
        $this->assertStringContainsString('john', $result[0]['result']);
    }
}
