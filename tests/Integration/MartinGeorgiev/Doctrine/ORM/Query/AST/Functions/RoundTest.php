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
        $dql = 'SELECT ROUND(3.14159) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsArrays t 
                WHERE t.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertIsInt($result[0]['result']);
        $this->assertSame(3, $result[0]['result']);
    }

    public function test_round_with_negative_number(): void
    {
        $dql = 'SELECT ROUND((-3.14159)) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsArrays t 
                WHERE t.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertIsInt($result[0]['result']);
        $this->assertSame(-3, $result[0]['result']);
    }

    public function test_round_with_precision(): void
    {
        $dql = 'SELECT ROUND(3.14159, 2) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsArrays t 
                WHERE t.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertIsFloat($result[0]['result']);
        $this->assertSame(3.14, $result[0]['result']);
    }

    public function test_round_with_negative_precision(): void
    {
        $dql = 'SELECT ROUND(314.159, (-2)) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsArrays t 
                WHERE t.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertIsInt($result[0]['result']);
        $this->assertSame(300, $result[0]['result']);
    }
}
