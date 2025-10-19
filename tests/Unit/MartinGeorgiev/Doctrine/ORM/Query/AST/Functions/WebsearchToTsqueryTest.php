<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsTexts;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BaseVariadicFunction;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Exception\InvalidArgumentForVariadicFunctionException;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\WebsearchToTsquery;
use PHPUnit\Framework\Attributes\Test;

class WebsearchToTsqueryTest extends BaseVariadicFunctionTestCase
{
    protected function createFixture(): BaseVariadicFunction
    {
        return new WebsearchToTsquery('WEBSEARCH_TO_TSQUERY');
    }

    protected function getStringFunctions(): array
    {
        return [
            'WEBSEARCH_TO_TSQUERY' => WebsearchToTsquery::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'converts text to tsquery with default config' => 'SELECT websearch_to_tsquery(c0_.text1) AS sclr_0 FROM ContainsTexts c0_',
            'converts function result to tsquery' => 'SELECT websearch_to_tsquery(UPPER(c0_.text1)) AS sclr_0 FROM ContainsTexts c0_',
            'converts text to tsquery with specified config' => "SELECT websearch_to_tsquery('english', c0_.text1) AS sclr_0 FROM ContainsTexts c0_",
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'converts text to tsquery with default config' => \sprintf('SELECT WEBSEARCH_TO_TSQUERY(e.text1) FROM %s e', ContainsTexts::class),
            'converts function result to tsquery' => \sprintf('SELECT WEBSEARCH_TO_TSQUERY(UPPER(e.text1)) FROM %s e', ContainsTexts::class),
            'converts text to tsquery with specified config' => \sprintf("SELECT WEBSEARCH_TO_TSQUERY('english', e.text1) FROM %s e", ContainsTexts::class),
        ];
    }

    #[Test]
    public function throws_exception_for_too_many_arguments(): void
    {
        $this->expectException(InvalidArgumentForVariadicFunctionException::class);

        $dql = \sprintf("SELECT WEBSEARCH_TO_TSQUERY('english', e.text1, 'extra') FROM %s e", ContainsTexts::class);
        $this->assertSqlFromDql('', $dql);
    }
}
