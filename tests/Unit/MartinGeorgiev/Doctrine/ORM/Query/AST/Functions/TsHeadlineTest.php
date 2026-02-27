<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsTexts;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BaseVariadicFunction;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Exception\InvalidArgumentForVariadicFunctionException;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\TsHeadline;
use PHPUnit\Framework\Attributes\Test;

class TsHeadlineTest extends BaseVariadicFunctionTestCase
{
    protected function createFixture(): BaseVariadicFunction
    {
        return new TsHeadline('TS_HEADLINE');
    }

    protected function getStringFunctions(): array
    {
        return [
            'TS_HEADLINE' => TsHeadline::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'highlights document against query string' => 'SELECT ts_headline(c0_.text1, c0_.text2) AS sclr_0 FROM ContainsTexts c0_',
            'highlights with config' => "SELECT ts_headline('english', c0_.text1, c0_.text2) AS sclr_0 FROM ContainsTexts c0_",
            'highlights with options' => "SELECT ts_headline(c0_.text1, c0_.text2, 'StartSel=<b>, StopSel=</b>') AS sclr_0 FROM ContainsTexts c0_",
            'highlights with config and options' => "SELECT ts_headline('english', c0_.text1, c0_.text2, 'MaxWords=5') AS sclr_0 FROM ContainsTexts c0_",
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'highlights document against query string' => \sprintf('SELECT TS_HEADLINE(e.text1, e.text2) FROM %s e', ContainsTexts::class),
            'highlights with config' => \sprintf("SELECT TS_HEADLINE('english', e.text1, e.text2) FROM %s e", ContainsTexts::class),
            'highlights with options' => \sprintf("SELECT TS_HEADLINE(e.text1, e.text2, 'StartSel=<b>, StopSel=</b>') FROM %s e", ContainsTexts::class),
            'highlights with config and options' => \sprintf("SELECT TS_HEADLINE('english', e.text1, e.text2, 'MaxWords=5') FROM %s e", ContainsTexts::class),
        ];
    }

    #[Test]
    public function throws_exception_for_too_many_arguments(): void
    {
        $this->expectException(InvalidArgumentForVariadicFunctionException::class);

        $dql = \sprintf("SELECT TS_HEADLINE('english', e.text1, e.text2, 'MaxWords=5', 'extra') FROM %s e", ContainsTexts::class);
        $this->assertSqlFromDql('', $dql);
    }
}
