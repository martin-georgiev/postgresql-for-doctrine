<?php

declare(strict_types=1);

namespace Tests\MartinGeorgiev\Utils;

use MartinGeorgiev\Utils\PHPArrayToPostgresRecordTransformer;
use MartinGeorgiev\Utils\PostgresRecordToPHPArrayTransformer;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class PHPArrayToPostgresRecordTransformerTest extends TestCase
{
    /**
     * @param array<int, string|null> $phpValue
     */
    #[DataProvider('provideValidTransformations')]
    #[Test]
    public function converts_to_postgres_value(array $phpValue, string $postgresValue): void
    {
        $this->assertSame($postgresValue, PHPArrayToPostgresRecordTransformer::transformPHPArrayToPostgresRecord($phpValue));
    }

    /**
     * Quoting only where PostgreSQL itself would quote is what keeps a stored value from drifting, so the
     * written literal must survive being read back unchanged.
     *
     * @param array<int, string|null> $phpValue
     */
    #[DataProvider('provideValidTransformations')]
    #[Test]
    public function roundtrips_through_the_reading_transformer(array $phpValue, string $postgresValue): void
    {
        $this->assertSame($phpValue, PostgresRecordToPHPArrayTransformer::transformPostgresRecordToPHPArray($postgresValue));
    }

    /**
     * @return array<string, array{phpValue: array<int, string|null>, postgresValue: string}>
     */
    public static function provideValidTransformations(): array
    {
        return [
            'plain fields need no quoting' => [
                'phpValue' => ['widget', '42', '9.99'],
                'postgresValue' => '(widget,42,9.99)',
            ],
            'null becomes an empty field' => [
                'phpValue' => [null, null],
                'postgresValue' => '(,)',
            ],
            'empty string is quoted' => [
                'phpValue' => ['', null],
                'postgresValue' => '("",)',
            ],
            'delimiter forces quoting' => [
                'phpValue' => ['a,b', '1'],
                'postgresValue' => '("a,b",1)',
            ],
            'parenthesis forces quoting' => [
                'phpValue' => ['(paren)', '1'],
                'postgresValue' => '("(paren)",1)',
            ],
            'whitespace forces quoting' => [
                'phpValue' => ['  padded  ', '1'],
                'postgresValue' => '("  padded  ",1)',
            ],
            'quote is doubled' => [
                'phpValue' => ['say "hi"', '1'],
                'postgresValue' => '("say ""hi""",1)',
            ],
            'backslash is escaped' => [
                'phpValue' => ['back\\slash', '1'],
                'postgresValue' => '("back\\\\slash",1)',
            ],
            'braces and quotes need no quoting' => [
                'phpValue' => ["a{b}c'd;e:f|g", '1'],
                'postgresValue' => "(a{b}c'd;e:f|g,1)",
            ],
            'value containing a newline and a tab' => [
                'phpValue' => ["line1\nline2\tend", '1'],
                'postgresValue' => "(\"line1\nline2\tend\",1)",
            ],
            'every special character at once' => [
                'phpValue' => ["a,b \"q\" c\\d (e) \n f", '1'],
                'postgresValue' => "(\"a,b \"\"q\"\" c\\\\d (e) \n f\",1)",
            ],
            'the literal word NULL is not quoted' => [
                'phpValue' => ['NULL', '1'],
                'postgresValue' => '(NULL,1)',
            ],
        ];
    }
}
