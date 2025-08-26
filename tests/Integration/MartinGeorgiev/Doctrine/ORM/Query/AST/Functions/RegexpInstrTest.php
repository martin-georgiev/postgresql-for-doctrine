<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\RegexpInstr;
use PHPUnit\Framework\Attributes\Test;

class RegexpInstrTest extends JsonTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'REGEXP_INSTR' => RegexpInstr::class,
        ];
    }

    #[Test]
    public function regexp_instr(): void
    {
        // NOTE: Using string literals for arguments due to DQL limitations with field extraction.
        $dql = "SELECT REGEXP_INSTR('John', 'J.*n') as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsJsons t WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertSame(1, $result[0]['result']);
    }

    #[Test]
    public function regexp_instr_negative(): void
    {
        $dql = "SELECT REGEXP_INSTR('John', 'Jane') as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsJsons t WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertSame(0, $result[0]['result']);
    }
}
