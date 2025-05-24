<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ToDate;

class ToDateTest extends JsonTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'to_date' => ToDate::class,
        ];
    }

    public function test_todate(): void
    {
        $dql = "SELECT to_date('05 Dec 2000', 'DD Mon YYYY') as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsJsons t WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        static::assertSame('2000-12-05', $result[0]['result']);
    }
}
