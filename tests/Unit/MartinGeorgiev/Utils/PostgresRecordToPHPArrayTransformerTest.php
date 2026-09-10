<?php

declare(strict_types=1);

namespace Tests\MartinGeorgiev\Utils;

use MartinGeorgiev\Utils\Exception\InvalidRecordFormatException;
use MartinGeorgiev\Utils\PostgresRecordToPHPArrayTransformer;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class PostgresRecordToPHPArrayTransformerTest extends TestCase
{
    /**
     * @param array<int, string|null> $phpValue
     */
    #[DataProvider('provideValidTransformations')]
    #[Test]
    public function converts_to_php_value(array $phpValue, string $postgresValue): void
    {
        $this->assertSame($phpValue, PostgresRecordToPHPArrayTransformer::transformPostgresRecordToPHPArray($postgresValue));
    }

    /**
     * @return array<string, array{phpValue: array<int, string|null>, postgresValue: string}>
     */
    public static function provideValidTransformations(): array
    {
        return [
            'plain fields' => [
                'phpValue' => ['widget', '42', '9.99'],
                'postgresValue' => '(widget,42,9.99)',
            ],
            'unquoted empty fields are null' => [
                'phpValue' => [null, null, null],
                'postgresValue' => '(,,)',
            ],
            'quoted empty field is an empty string' => [
                'phpValue' => ['', null, null],
                'postgresValue' => '("",,)',
            ],
            'unquoted NULL is the four-character string' => [
                'phpValue' => ['NULL', '1'],
                'postgresValue' => '(NULL,1)',
            ],
            'quoted field holding a delimiter' => [
                'phpValue' => ['a,b', '1'],
                'postgresValue' => '("a,b",1)',
            ],
            'doubled quote is a literal quote' => [
                'phpValue' => ['say "hi"', '1'],
                'postgresValue' => '("say ""hi""",1)',
            ],
            'backslash escape inside quotes' => [
                'phpValue' => ['back\\slash', '1'],
                'postgresValue' => '("back\\\\slash",1)',
            ],
            'backslash escape outside quotes' => [
                'phpValue' => ['a,b', '1'],
                'postgresValue' => '(a\\,b,1)',
            ],
            'surrounding whitespace is trimmed' => [
                'phpValue' => ['a', 'b'],
                'postgresValue' => '  (a,b)  ',
            ],
            'single field' => [
                'phpValue' => ['only'],
                'postgresValue' => '(only)',
            ],
            'nested record arrives as one raw field' => [
                'phpValue' => ['bob', '("1 Main St",Sofia)'],
                'postgresValue' => '(bob,"(""1 Main St"",Sofia)")',
            ],
            'bare opening parenthesis is an ordinary character' => [
                'phpValue' => ['a(b', '1'],
                'postgresValue' => '(a(b,1)',
            ],
            'escaped closing parenthesis outside quotes' => [
                'phpValue' => ['a)b', '1'],
                'postgresValue' => '(a\\)b,1)',
            ],
        ];
    }

    #[DataProvider('provideInvalidRecordLiterals')]
    #[Test]
    public function throws_exception_for_invalid_record_literal(string $postgresValue): void
    {
        $this->expectException(InvalidRecordFormatException::class);

        PostgresRecordToPHPArrayTransformer::transformPostgresRecordToPHPArray($postgresValue);
    }

    /**
     * @return array<string, array{string}>
     */
    public static function provideInvalidRecordLiterals(): array
    {
        return [
            'empty string' => [''],
            'missing both parentheses' => ['a,b'],
            'missing opening parenthesis' => ['a,b)'],
            'missing closing parenthesis' => ['(a,b'],
            'unterminated quoted field' => ['("a,b)'],
            'trailing backslash' => ['(a\\'],
            'escape consuming the closing parenthesis' => ['(a\\)'],
            'bare closing parenthesis ends the record early' => ['(a)b,1)'],
            'nested parentheses left unquoted' => ['((a),1)'],
        ];
    }
}
