<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Ltree;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsTexts;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Exception\InvalidArgumentForVariadicFunctionException;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Ltree\Index;
use PHPUnit\Framework\Attributes\Test;
use Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\TestCase;

class IndexTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'INDEX' => Index::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'finds position of ltree in another ltree' => 'SELECT index(c0_.text1, c0_.text2) AS sclr_0 FROM ContainsTexts c0_',
            'finds position with offset' => 'SELECT index(c0_.text1, c0_.text2, -4) AS sclr_0 FROM ContainsTexts c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'finds position of ltree in another ltree' => \sprintf('SELECT INDEX(e.text1, e.text2) FROM %s e', ContainsTexts::class),
            'finds position with offset' => \sprintf('SELECT INDEX(e.text1, e.text2, -4) FROM %s e', ContainsTexts::class),
        ];
    }

    #[Test]
    public function throws_exception_when_argument_count_is_too_low(): void
    {
        $this->expectException(InvalidArgumentForVariadicFunctionException::class);
        $this->expectExceptionMessage('index() requires at least 2 arguments');

        $dql = \sprintf('SELECT INDEX(e.text1) FROM %s e', ContainsTexts::class);
        $this->buildEntityManager()->createQuery($dql)->getSQL();
    }

    #[Test]
    public function throws_exception_when_argument_count_is_too_high(): void
    {
        $this->expectException(InvalidArgumentForVariadicFunctionException::class);
        $this->expectExceptionMessage('index() requires between 2 and 3 arguments');

        $dql = \sprintf('SELECT INDEX(e.text1, e.text2, 0, 1) FROM %s e', ContainsTexts::class);
        $this->buildEntityManager()->createQuery($dql)->getSQL();
    }
}
