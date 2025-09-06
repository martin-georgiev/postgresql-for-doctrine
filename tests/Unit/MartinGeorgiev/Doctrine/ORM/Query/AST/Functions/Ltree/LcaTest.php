<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Ltree;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsTexts;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Exception\InvalidArgumentForVariadicFunctionException;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Ltree\Lca;
use PHPUnit\Framework\Attributes\Test;
use Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\TestCase;

class LcaTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'LCA' => Lca::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'computes longest common ancestor of two paths' => 'SELECT lca(c0_.text1, c0_.text2) AS sclr_0 FROM ContainsTexts c0_',
            'computes longest common ancestor of three paths' => "SELECT lca(c0_.text1, c0_.text2, 'Top.Child1') AS sclr_0 FROM ContainsTexts c0_",
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'computes longest common ancestor of two paths' => \sprintf('SELECT LCA(e.text1, e.text2) FROM %s e', ContainsTexts::class),
            'computes longest common ancestor of three paths' => \sprintf("SELECT LCA(e.text1, e.text2, 'Top.Child1') FROM %s e", ContainsTexts::class),
        ];
    }

    #[Test]
    public function throws_exception_when_argument_count_is_too_low(): void
    {
        $this->expectException(InvalidArgumentForVariadicFunctionException::class);
        $this->expectExceptionMessage('lca() requires at least 2 arguments');

        $dql = \sprintf('SELECT LCA(e.text1) FROM %s e', ContainsTexts::class);
        $this->buildEntityManager()->createQuery($dql)->getSQL();
    }

    #[Test]
    public function throws_exception_when_argument_count_is_too_high(): void
    {
        $this->expectException(InvalidArgumentForVariadicFunctionException::class);
        $this->expectExceptionMessage('lca() requires between 2 and 8 arguments');

        $dql = \sprintf('SELECT LCA(e.text1, e.text2, e.text3, e.text4, e.text5, e.text6, e.text7, e.text8, e.text9) FROM %s e', ContainsTexts::class);
        $this->buildEntityManager()->createQuery($dql)->getSQL();
    }
}
