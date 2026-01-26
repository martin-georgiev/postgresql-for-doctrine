<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayToJsonb;
use PHPUnit\Framework\Attributes\Test;

class ArrayToJsonbTest extends ArrayTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ARRAY_TO_JSONB' => ArrayToJsonb::class,
        ];
    }

    #[Test]
    public function can_convert_array_to_jsonb(): void
    {
        $dql = 'SELECT ARRAY_TO_JSONB(t.textArray) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsArrays t 
                WHERE t.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertIsString($result[0]['result']);
        $decoded = \json_decode($result[0]['result'], true);
        $this->assertIsArray($decoded);
        $this->assertSame(['apple', 'banana', 'orange'], $decoded);
    }
}
