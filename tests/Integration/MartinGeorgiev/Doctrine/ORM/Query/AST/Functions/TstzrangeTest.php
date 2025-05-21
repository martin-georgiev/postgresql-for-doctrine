<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Tstzrange;

class TstzrangeTest extends JsonTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'TSTZRANGE' => Tstzrange::class,
        ];
    }

    public function test_tstzrange(): void
    {
        $dql = "SELECT TSTZRANGE('2024-01-01 00:00:00+00', '2024-12-31 23:59:59+00') as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsJsons t WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertIsArray($result);
        $this->assertNotEmpty($result[0]['result']);
        $this->assertSame('["2024-01-01 00:00:00+00","2024-12-31 23:59:59+00")', (string) $result[0]['result']);
    }
}
