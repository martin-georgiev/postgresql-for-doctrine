<?php

declare(strict_types=1);

namespace Tests\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsTexts;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Exception\InvalidArgumentForVariadicFunctionException;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ToTsvector;

class ToTsvectorTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'TO_TSVECTOR' => ToTsvector::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'SELECT to_tsvector(c0_.text1) AS sclr_0 FROM ContainsTexts c0_',
            'SELECT to_tsvector(LOWER(c0_.text1)) AS sclr_0 FROM ContainsTexts c0_',
            'SELECT to_tsvector(\'english\', c0_.text1) AS sclr_0 FROM ContainsTexts c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            \sprintf('SELECT TO_TSVECTOR(e.text1) FROM %s e', ContainsTexts::class),
            \sprintf('SELECT TO_TSVECTOR(LOWER(e.text1)) FROM %s e', ContainsTexts::class),
            \sprintf('SELECT TO_TSVECTOR(\'english\', e.text1) FROM %s e', ContainsTexts::class),
        ];
    }

    /**
     * @test
     */
    public function throws_exception_when_too_many_arguments_given(): void
    {
        $this->expectException(InvalidArgumentForVariadicFunctionException::class);

        $dql = \sprintf('SELECT TO_TSVECTOR(\'english\', e.text1, \'extra\') FROM %s e', ContainsTexts::class);
        $this->assertSqlFromDql('', $dql);
    }
}
