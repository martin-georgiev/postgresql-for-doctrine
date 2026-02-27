<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsTexts;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BaseVariadicFunction;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Exception\InvalidArgumentForVariadicFunctionException;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\TsRankCd;
use PHPUnit\Framework\Attributes\Test;

class TsRankCdTest extends BaseVariadicFunctionTestCase
{
    protected function createFixture(): BaseVariadicFunction
    {
        return new TsRankCd('TS_RANK_CD');
    }

    protected function getStringFunctions(): array
    {
        return [
            'TS_RANK_CD' => TsRankCd::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'ranks two text fields using cover density' => 'SELECT ts_rank_cd(c0_.text1, c0_.text2) AS sclr_0 FROM ContainsTexts c0_',
            'ranks with normalization flag' => 'SELECT ts_rank_cd(c0_.text1, c0_.text2, 1) AS sclr_0 FROM ContainsTexts c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'ranks two text fields using cover density' => \sprintf('SELECT TS_RANK_CD(e.text1, e.text2) FROM %s e', ContainsTexts::class),
            'ranks with normalization flag' => \sprintf('SELECT TS_RANK_CD(e.text1, e.text2, 1) FROM %s e', ContainsTexts::class),
        ];
    }

    #[Test]
    public function throws_exception_for_too_many_arguments(): void
    {
        $this->expectException(InvalidArgumentForVariadicFunctionException::class);

        $dql = \sprintf('SELECT TS_RANK_CD(e.text1, e.text2, 1, 2) FROM %s e', ContainsTexts::class);
        $this->assertSqlFromDql('', $dql);
    }
}
