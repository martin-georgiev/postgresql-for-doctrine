<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ToTimestamp;

class ToTimestampTest extends JsonTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'to_timestamp' => ToTimestamp::class,
        ];
    }

    public function test_totimestamp(): void
    {
        $dql = "SELECT to_timestamp('05 Dec 2000', 'DD Mon YYYY') as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsJsons t WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        static::assertSame('2000-12-05 00:00:00+00', $result[0]['result']);
    }
}
