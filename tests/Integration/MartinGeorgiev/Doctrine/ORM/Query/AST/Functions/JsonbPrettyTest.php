<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonbPretty;
use PHPUnit\Framework\Attributes\Test;

class JsonbPrettyTest extends JsonTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'JSONB_PRETTY' => JsonbPretty::class,
        ];
    }

    #[Test]
    public function jsonb_pretty(): void
    {
        $dql = 'SELECT JSONB_PRETTY(t.object1) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsJsons t 
                WHERE t.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertIsString($result[0]['result']);
    }
}
