<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonbToArray;
use PHPUnit\Framework\Attributes\Test;

class JsonbToArrayTest extends JsonTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->requirePostgresVersion(180000, 'jsonb_to_array function');
    }

    protected function getStringFunctions(): array
    {
        return [
            'JSONB_TO_ARRAY' => JsonbToArray::class,
        ];
    }

    #[Test]
    public function can_convert_jsonb_array_to_postgres_array(): void
    {
        $dql = "SELECT JSONB_TO_ARRAY('[\"a\", \"b\", \"c\"]'::jsonb) as result 
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsJsons t 
                WHERE t.id = 1";

        $result = $this->executeDqlQuery($dql);
        $actual = $this->transformPostgresArray($result[0]['result']);
        $this->assertIsArray($actual);
        $this->assertSame(['"a"', '"b"', '"c"'], $actual);
    }
}
