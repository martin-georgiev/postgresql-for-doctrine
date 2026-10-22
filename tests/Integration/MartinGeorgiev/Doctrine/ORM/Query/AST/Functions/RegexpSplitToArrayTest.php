<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\RegexpSplitToArray;
use PHPUnit\Framework\Attributes\Test;

final class RegexpSplitToArrayTest extends TextTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'REGEXP_SPLIT_TO_ARRAY' => RegexpSplitToArray::class,
        ];
    }

    #[Test]
    public function returns_the_split_parts_from_a_text_literal(): void
    {
        $dql = "SELECT REGEXP_SPLIT_TO_ARRAY('special,chars;test', '[,;]') as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t
                WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertSame('{special,chars,test}', $result[0]['result']);
    }

    #[Test]
    public function returns_the_split_parts_from_an_entity_field(): void
    {
        $dql = "SELECT REGEXP_SPLIT_TO_ARRAY(t.text1, ' ') as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t
                WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertSame('{this,is,a,test,string}', $result[0]['result']);
    }

    #[Test]
    public function returns_the_split_parts_with_flags(): void
    {
        $dql = "SELECT REGEXP_SPLIT_TO_ARRAY(t.text1, ' IS ', 'i') as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t
                WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertSame('{this,"a test string"}', $result[0]['result']);
    }
}
