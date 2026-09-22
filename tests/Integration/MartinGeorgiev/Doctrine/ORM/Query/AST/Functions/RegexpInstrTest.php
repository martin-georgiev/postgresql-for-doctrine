<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\RegexpInstr;
use PHPUnit\Framework\Attributes\Test;

final class RegexpInstrTest extends TextTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'REGEXP_INSTR' => RegexpInstr::class,
        ];
    }

    #[Test]
    public function returns_the_match_position_from_an_entity_field(): void
    {
        $dql = "SELECT REGEXP_INSTR(t.text1, 'test') as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertSame(11, $result[0]['result']);
    }

    #[Test]
    public function returns_the_match_position_from_a_literal(): void
    {
        $dql = "SELECT REGEXP_INSTR('this is a test string', 'test') as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertSame(11, $result[0]['result']);
    }
}
