<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Utils;

use MartinGeorgiev\Utils\PostgresBackslashEscaper;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class PostgresBackslashEscaperTest extends TestCase
{
    #[DataProvider('provideValuesNeedingEscaping')]
    #[Test]
    public function converts_to_escaped_value(string $phpValue, string $escapedValue): void
    {
        $this->assertSame($escapedValue, PostgresBackslashEscaper::escape($phpValue));
    }

    #[DataProvider('provideValuesNeedingEscaping')]
    #[Test]
    public function converts_from_escaped_value(string $phpValue, string $escapedValue): void
    {
        $this->assertSame($phpValue, PostgresBackslashEscaper::unescape($escapedValue));
    }

    /**
     * @return array<string, array{phpValue: string, escapedValue: string}>
     */
    public static function provideValuesNeedingEscaping(): array
    {
        return [
            'nothing to escape' => ['phpValue' => 'plain', 'escapedValue' => 'plain'],
            'empty string' => ['phpValue' => '', 'escapedValue' => ''],
            'a quote' => ['phpValue' => 'a"b', 'escapedValue' => 'a\\"b'],
            'a backslash' => ['phpValue' => 'a\\b', 'escapedValue' => 'a\\\\b'],
            'a backslash before a quote' => ['phpValue' => 'a\\"b', 'escapedValue' => 'a\\\\\\"b'],
            'two backslashes' => ['phpValue' => 'a\\\\b', 'escapedValue' => 'a\\\\\\\\b'],
            'only a backslash' => ['phpValue' => '\\', 'escapedValue' => '\\\\'],
        ];
    }

    #[Test]
    public function converts_a_lone_backslash_as_an_escape(): void
    {
        $this->assertSame('ab', PostgresBackslashEscaper::unescape('a\\b'));
    }

    #[Test]
    public function preserves_a_trailing_backslash(): void
    {
        $this->assertSame('a\\', PostgresBackslashEscaper::unescape('a\\'));
    }
}
