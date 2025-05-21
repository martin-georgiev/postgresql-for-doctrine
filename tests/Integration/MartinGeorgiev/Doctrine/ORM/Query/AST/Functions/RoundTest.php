<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Round;

class RoundTest extends ArrayTestCase
{
    protected function getStringFunctions(): array
    {
        return ['ROUND' => Round::class];
    }

    public function test_round_with_positive_number(): void
    {
        $dql = 'SELECT ROUND(:number) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsArrays t 
                WHERE t.id = 1';
        $result = $this->executeDqlQuery($dql, ['number' => 3.14159]);
        $this->assertIsNumeric($result[0]['result']);
        $this->assertSame(3.0, (float) $result[0]['result']);
    }

    public function test_round_with_negative_number(): void
    {
        $dql = 'SELECT ROUND(:number) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsArrays t 
                WHERE t.id = 1';
        $result = $this->executeDqlQuery($dql, ['number' => -3.14159]);
        $this->assertIsNumeric($result[0]['result']);
        $this->assertSame(-3.0, (float) $result[0]['result']);
    }

    public function test_round_with_precision(): void
    {
        $dql = 'SELECT ROUND(:number, :precision) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsArrays t 
                WHERE t.id = 1';
        $result = $this->executeDqlQuery($dql, [
            'number' => 3.14159,
            'precision' => 2,
        ]);
        $this->assertIsNumeric($result[0]['result']);
        $this->assertSame(3.14, (float) $result[0]['result']);
    }

    public function test_round_with_negative_precision(): void
    {
        $dql = 'SELECT ROUND(:number, :precision) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsArrays t 
                WHERE t.id = 1';
        $result = $this->executeDqlQuery($dql, [
            'number' => 314.159,
            'precision' => -2,
        ]);
        $this->assertIsNumeric($result[0]['result']);
        $this->assertSame(300.0, (float) $result[0]['result']);
    }
}
