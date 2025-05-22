<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Int8range;

class Int8rangeTest extends NumericTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'INT8RANGE' => Int8range::class,
        ];
    }

    public function test_int8range(): void
    {
        $dql = 'SELECT INT8RANGE(t.bigint1, t.bigint2) as result FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics t WHERE t.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertIsArray($result);
        $this->assertNotEmpty($result[0]['result']);
        $this->assertIsString($result[0]['result']);
        $this->assertSame('[1000,2000)', $result[0]['result']);
    }

    public function test_int8range_with_bounds(): void
    {
        $dql = "SELECT INT8RANGE(t.bigint1, t.bigint2, '[)') as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsNumerics t WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertIsArray($result);
        $this->assertNotEmpty($result[0]['result']);
        $this->assertIsString($result[0]['result']);
        $this->assertSame('[1000,2000)', $result[0]['result']);
    }
}
