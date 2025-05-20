<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Trunc;

class TruncTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return ['TRUNC' => Trunc::class];
    }

    public function test_trunc_with_positive_number(): void
    {
        $dql = 'SELECT TRUNC(3.14159) as result FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsArrays t WHERE t.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(3, $result[0]['result']);
    }

    public function test_trunc_with_negative_number(): void
    {
        $dql = 'SELECT TRUNC(-3.14159) as result FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsArrays t WHERE t.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(-3, $result[0]['result']);
    }

    public function test_trunc_with_precision(): void
    {
        $dql = 'SELECT TRUNC(3.14159, 2) as result FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsArrays t WHERE t.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(3.14, $result[0]['result']);
    }

    public function test_trunc_with_negative_precision(): void
    {
        $dql = 'SELECT TRUNC(314.159, -2) as result FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsArrays t WHERE t.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(300, $result[0]['result']);
    }
}
