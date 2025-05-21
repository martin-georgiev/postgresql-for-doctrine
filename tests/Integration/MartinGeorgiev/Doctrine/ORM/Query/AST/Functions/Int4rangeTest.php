<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Int4range;

class Int4rangeTest extends JsonTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'INT4RANGE' => Int4range::class,
        ];
    }

    public function test_int4range(): void
    {
        $dql = "SELECT INT4RANGE(:start, :end) as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsJsons t WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql, [
            'start' => 10,
            'end' => 20,
        ]);
        $this->assertIsArray($result);
        $this->assertNotEmpty($result[0]['result']);
        $actual = (string)($result[0]['result'] ?? '');
        $this->assertSame('[10,20)', $actual);
    }

    public function test_int4range_with_bounds(): void
    {
        $dql = "SELECT INT4RANGE(:start, :end, '[)') as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsJsons t WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql, [
            'start' => 10,
            'end' => 20,
        ]);
        $this->assertIsArray($result);
        $this->assertNotEmpty($result[0]['result']);
        $actual = (string)($result[0]['result'] ?? '');
        $this->assertSame('[10,20)', $actual);
    }
}
