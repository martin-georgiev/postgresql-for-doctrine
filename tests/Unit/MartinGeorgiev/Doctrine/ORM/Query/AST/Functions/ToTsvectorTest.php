<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsTexts;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BaseVariadicFunction;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Exception\InvalidArgumentForVariadicFunctionException;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ToTsvector;

class ToTsvectorTest extends BaseVariadicFunctionTestCase
{
    protected function createFixture(): BaseVariadicFunction
    {
        return new ToTsvector('TO_TSVECTOR');
    }

    protected function getStringFunctions(): array
    {
        return [
            'TO_TSVECTOR' => ToTsvector::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'converts plain text to tsvector' => 'SELECT to_tsvector(c0_.text1) AS sclr_0 FROM ContainsTexts c0_',
            'converts lowercase text to tsvector' => 'SELECT to_tsvector(LOWER(c0_.text1)) AS sclr_0 FROM ContainsTexts c0_',
            'converts text to tsvector using english dictionary' => "SELECT to_tsvector('english', c0_.text1) AS sclr_0 FROM ContainsTexts c0_",
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'converts plain text to tsvector' => \sprintf('SELECT TO_TSVECTOR(e.text1) FROM %s e', ContainsTexts::class),
            'converts lowercase text to tsvector' => \sprintf('SELECT TO_TSVECTOR(LOWER(e.text1)) FROM %s e', ContainsTexts::class),
            'converts text to tsvector using english dictionary' => \sprintf("SELECT TO_TSVECTOR('english', e.text1) FROM %s e", ContainsTexts::class),
        ];
    }

    /**
     * @test
     */
    public function throws_exception_when_too_many_arguments_given(): void
    {
        $this->expectException(InvalidArgumentForVariadicFunctionException::class);

        $dql = \sprintf("SELECT TO_TSVECTOR('english', e.text1, 'extra') FROM %s e", ContainsTexts::class);
        $this->assertSqlFromDql('', $dql);
    }
}
