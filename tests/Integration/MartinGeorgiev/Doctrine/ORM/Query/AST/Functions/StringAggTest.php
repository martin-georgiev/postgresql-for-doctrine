<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\StringAgg;

class StringAggTest extends TextTestCase
{
    protected function getStringFunctions(): array
    {
        return ['STRING_AGG' => StringAgg::class];
    }

    public function test_string_agg_with_for_all_rows(): void
    {
        $dql = "SELECT STRING_AGG(t.text1, ',') as result 
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t";
        $result = $this->executeDqlQuery($dql);
        $this->assertSame('this is a test string,lorem ipsum dolor,foo,special,chars;test', $result[0]['result']);
    }

    public function test_string_agg_with_for_all_rows_and_semicolon_delimiter(): void
    {
        $dql = "SELECT STRING_AGG(t.text2, ';') as result 
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t";
        $result = $this->executeDqlQuery($dql);
        $this->assertSame('another test string;sit amet;bar;multi;delimiter,case', $result[0]['result']);
    }

    public function test_string_agg_with_for_all_rows_and_space_delimiter(): void
    {
        $dql = "SELECT STRING_AGG(t.text1, ' ') as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t";
        $result = $this->executeDqlQuery($dql);
        $this->assertSame('this is a test string lorem ipsum dolor foo special,chars;test', $result[0]['result']);
    }

    public function test_string_agg_with_where_clause(): void
    {
        $dql = "SELECT STRING_AGG(t.text1, ',') as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t WHERE t.id in (1, 4)";
        $result = $this->executeDqlQuery($dql);
        $this->assertSame('this is a test string,special,chars;test', $result[0]['result']);
    }
}
