<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\RegexpReplace;
use PHPUnit\Framework\Attributes\Test;

final class RegexpReplaceTest extends TextTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'REGEXP_REPLACE' => RegexpReplace::class,
        ];
    }

    #[Test]
    public function returns_the_replaced_value_from_an_entity_field(): void
    {
        $dql = "SELECT REGEXP_REPLACE(t.text1, 'test', 'replaced') as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertSame('this is a replaced string', $result[0]['result']);
    }

    #[Test]
    public function returns_the_replaced_value_from_a_literal_with_a_flags_argument(): void
    {
        $dql = "SELECT REGEXP_REPLACE('this is a test string', 'is', 'was', 'g') as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertSame('thwas was a test string', $result[0]['result']);
    }
}
