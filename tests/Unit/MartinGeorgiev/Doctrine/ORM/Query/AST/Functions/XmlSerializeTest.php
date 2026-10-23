<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Doctrine\ORM\Query\QueryException;
use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsTexts;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\XmlSerialize;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

final class XmlSerializeTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'XMLSERIALIZE' => XmlSerialize::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'serializes a document' => 'SELECT xmlserialize(DOCUMENT c0_.text1 AS TEXT) AS sclr_0 FROM ContainsTexts c0_',
            'serializes content' => 'SELECT xmlserialize(CONTENT c0_.text1 AS VARCHAR) AS sclr_0 FROM ContainsTexts c0_',
            'serializes a literal' => "SELECT xmlserialize(DOCUMENT '<a/>' AS TEXT) AS sclr_0 FROM ContainsTexts c0_",
            'serializes with indent' => 'SELECT xmlserialize(DOCUMENT c0_.text1 AS TEXT INDENT) AS sclr_0 FROM ContainsTexts c0_',
            'serializes with no indent' => 'SELECT xmlserialize(DOCUMENT c0_.text1 AS TEXT NO INDENT) AS sclr_0 FROM ContainsTexts c0_',
            'serializes with lowercase keywords' => 'SELECT xmlserialize(CONTENT c0_.text1 AS TEXT INDENT) AS sclr_0 FROM ContainsTexts c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'serializes a document' => \sprintf('SELECT XMLSERIALIZE(DOCUMENT e.text1 AS TEXT) FROM %s e', ContainsTexts::class),
            'serializes content' => \sprintf('SELECT XMLSERIALIZE(CONTENT e.text1 AS VARCHAR) FROM %s e', ContainsTexts::class),
            'serializes a literal' => \sprintf("SELECT XMLSERIALIZE(DOCUMENT '<a/>' AS TEXT) FROM %s e", ContainsTexts::class),
            'serializes with indent' => \sprintf('SELECT XMLSERIALIZE(DOCUMENT e.text1 AS TEXT INDENT) FROM %s e', ContainsTexts::class),
            'serializes with no indent' => \sprintf('SELECT XMLSERIALIZE(DOCUMENT e.text1 AS TEXT NO INDENT) FROM %s e', ContainsTexts::class),
            'serializes with lowercase keywords' => \sprintf('SELECT XMLSERIALIZE(content e.text1 as text indent) FROM %s e', ContainsTexts::class),
        ];
    }

    #[DataProvider('provideMalformedClauses')]
    #[Test]
    public function throws_exception_for_malformed_clause(string $dql): void
    {
        $this->expectException(QueryException::class);

        $this->buildEntityManager()->createQuery($dql)->getSQL();
    }

    /**
     * @return array<string, array{string}>
     */
    public static function provideMalformedClauses(): array
    {
        return [
            'missing document or content' => [\sprintf('SELECT XMLSERIALIZE(e.text1 AS TEXT) FROM %s e', ContainsTexts::class)],
            'unknown option' => [\sprintf('SELECT XMLSERIALIZE(FRAGMENT e.text1 AS TEXT) FROM %s e', ContainsTexts::class)],
            'missing target type' => [\sprintf('SELECT XMLSERIALIZE(DOCUMENT e.text1) FROM %s e', ContainsTexts::class)],
            'unsupported target type' => [\sprintf('SELECT XMLSERIALIZE(DOCUMENT e.text1 AS INTEGER) FROM %s e', ContainsTexts::class)],
            'no without indent' => [\sprintf('SELECT XMLSERIALIZE(DOCUMENT e.text1 AS TEXT NO) FROM %s e', ContainsTexts::class)],
            'unknown word after the type' => [\sprintf('SELECT XMLSERIALIZE(DOCUMENT e.text1 AS TEXT PRETTY) FROM %s e', ContainsTexts::class)],
        ];
    }
}
