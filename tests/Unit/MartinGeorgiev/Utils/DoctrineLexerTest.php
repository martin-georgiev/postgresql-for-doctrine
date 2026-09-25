<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Utils;

use MartinGeorgiev\Utils\DoctrineLexer;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class DoctrineLexerTest extends TestCase
{
    #[DataProvider('provideTokenFields')]
    #[Test]
    public function returns_the_token_field(mixed $token, string $field, mixed $expectedValue): void
    {
        $this->assertSame($expectedValue, DoctrineLexer::getTokenField($token, $field));
    }

    /**
     * @return array<string, array{mixed, string, mixed}>
     */
    public static function provideTokenFields(): array
    {
        return [
            'value of an array token' => [['value' => 'ROWS', 'type' => 100, 'position' => 0], 'value', 'ROWS'],
            'type of an array token' => [['value' => 'ROWS', 'type' => 100, 'position' => 0], 'type', 100],
            'missing field of an array token' => [['value' => 'ROWS'], 'type', null],
            'value of an object token' => [self::createObjectToken(), 'value', 'ROWS'],
            'missing field of an object token' => [self::createObjectToken(), 'length', null],
            'no token' => [null, 'value', null],
        ];
    }

    /**
     * Shaped like Lexer 2.0+'s Token, which Lexer 1.x does not ship.
     */
    private static function createObjectToken(): object
    {
        return new class {
            public string $value = 'ROWS';

            public int $type = 100;

            public int $position = 0;
        };
    }
}
