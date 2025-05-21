<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Int8range;

class Int8rangeTest extends JsonTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'INT8RANGE' => Int8range::class,
        ];
    }

    public function test_int8range(): void
    {
        $dql = 'SELECT INT8RANGE(:start, :end) as result FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsJsons t WHERE t.id = 1';
        $result = $this->executeDqlQuery($dql, [
            'start' => 100,
            'end' => 200,
        ]);
        $this->assertIsArray($result);
        $this->assertNotEmpty($result[0]['result']);
        $this->assertIsString($result[0]['result']);
        $this->assertSame('[100,200)', $result[0]['result']);
    }

    public function test_int8range_with_bounds(): void
    {
        $dql = "SELECT INT8RANGE(:start, :end, '[)') as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsJsons t WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql, [
            'start' => 100,
            'end' => 200,
        ]);
        $this->assertIsArray($result);
        $this->assertNotEmpty($result[0]['result']);
        $this->assertIsString($result[0]['result']);
        $this->assertSame('[100,200)', $result[0]['result']);
    }
}
