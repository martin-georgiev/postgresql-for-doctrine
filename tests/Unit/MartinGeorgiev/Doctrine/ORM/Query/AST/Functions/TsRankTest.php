<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsTexts;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BaseVariadicFunction;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Exception\InvalidArgumentForVariadicFunctionException;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\TsRank;
use PHPUnit\Framework\Attributes\Test;

class TsRankTest extends BaseVariadicFunctionTestCase
{
    protected function createFixture(): BaseVariadicFunction
    {
        return new TsRank('TS_RANK');
    }

    protected function getStringFunctions(): array
    {
        return [
            'TS_RANK' => TsRank::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'ranks two text fields' => 'SELECT ts_rank(c0_.text1, c0_.text2) AS sclr_0 FROM ContainsTexts c0_',
            'ranks with normalization flag' => 'SELECT ts_rank(c0_.text1, c0_.text2, 1) AS sclr_0 FROM ContainsTexts c0_',
            'ranks with weights and normalization flag' => "SELECT ts_rank('{0.1,0.2,0.4,1.0}', c0_.text2, c0_.text1, 1) AS sclr_0 FROM ContainsTexts c0_",
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'ranks two text fields' => \sprintf('SELECT TS_RANK(e.text1, e.text2) FROM %s e', ContainsTexts::class),
            'ranks with normalization flag' => \sprintf('SELECT TS_RANK(e.text1, e.text2, 1) FROM %s e', ContainsTexts::class),
            'ranks with weights and normalization flag' => \sprintf("SELECT TS_RANK('{0.1,0.2,0.4,1.0}', e.text2, e.text1, 1) FROM %s e", ContainsTexts::class),
        ];
    }

    #[Test]
    public function throws_exception_for_too_many_arguments(): void
    {
        $this->expectException(InvalidArgumentForVariadicFunctionException::class);

        $dql = \sprintf("SELECT TS_RANK('{0.1,0.2,0.4,1.0}', e.text2, e.text1, 1, 2) FROM %s e", ContainsTexts::class);
        $this->assertSqlFromDql('', $dql);
    }
}
