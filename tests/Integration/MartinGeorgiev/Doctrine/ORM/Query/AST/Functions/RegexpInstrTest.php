<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\RegexpInstr;

class RegexpInstrTest extends JsonTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'REGEXP_INSTR' => RegexpInstr::class,
        ];
    }

    public function test_regexp_instr(): void
    {
        // NOTE: Using string literals for arguments due to DQL limitations with field extraction.
        $dql = "SELECT REGEXP_INSTR('John', 'J.*n') as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsJsons t WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertIsArray($result);
        $this->assertIsInt($result[0]['result']);
        $this->assertSame(1, $result[0]['result']);
    }

    public function test_regexp_instr_negative(): void
    {
        $dql = "SELECT REGEXP_INSTR('John', 'Jane') as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsJsons t WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertIsArray($result);
        $this->assertIsInt($result[0]['result']);
        $this->assertSame(0, $result[0]['result']);
    }
}
