<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Int8range;
use PHPUnit\Framework\Attributes\Test;

class Int8rangeTest extends NumericTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'INT8RANGE' => Int8range::class,
        ];
    }

    #[Test]
    public function int8range(): void
    {
        $dql = 'SELECT INT8RANGE(t.bigint1, t.bigint2) as result FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics t WHERE t.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertSame('[1000,2000)', $result[0]['result']);
    }

    #[Test]
    public function int8range_with_bounds(): void
    {
        $dql = "SELECT INT8RANGE(t.bigint1, t.bigint2, '(]') as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsNumerics t WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertSame('[1001,2001)', $result[0]['result']);
    }
}
