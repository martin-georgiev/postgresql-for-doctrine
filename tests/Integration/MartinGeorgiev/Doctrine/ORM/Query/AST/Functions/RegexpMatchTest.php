<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\RegexpMatch;
use PHPUnit\Framework\Attributes\Test;

final class RegexpMatchTest extends TextTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'REGEXP_MATCH' => RegexpMatch::class,
        ];
    }

    #[Test]
    public function returns_the_matching_substrings_from_an_entity_field(): void
    {
        $dql = "SELECT REGEXP_MATCH(t.text1, 'test') as result 
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t 
                WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertSame('{test}', $result[0]['result']);
    }

    #[Test]
    public function returns_the_matching_substrings_from_a_literal_with_a_flags_argument(): void
    {
        $dql = "SELECT REGEXP_MATCH('this is a test string', 'TEST', 'i') as result 
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t 
                WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertSame('{test}', $result[0]['result']);
    }
}
