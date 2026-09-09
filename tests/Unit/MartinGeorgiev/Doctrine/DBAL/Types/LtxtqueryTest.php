<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidLtxtqueryForDatabaseException;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidLtxtqueryForPHPException;
use MartinGeorgiev\Doctrine\DBAL\Types\Ltxtquery;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;

final class LtxtqueryTest extends TestCase
{
    /**
     * @var AbstractPlatform&Stub
     */
    private Stub $platform;

    private Ltxtquery $fixture;

    protected function setUp(): void
    {
        $this->platform = $this->createStub(AbstractPlatform::class);
        $this->fixture = new Ltxtquery();
    }

    #[Test]
    public function has_name(): void
    {
        $this->assertSame('ltxtquery', $this->fixture->getName());
    }

    #[DataProvider('provideValidTransformations')]
    #[Test]
    public function converts_to_database_value(?string $phpValue, ?string $databaseValue): void
    {
        $this->assertSame($databaseValue, $this->fixture->convertToDatabaseValue($phpValue, $this->platform));
    }

    #[DataProvider('provideValidTransformations')]
    #[Test]
    public function converts_to_php_value(?string $phpValue, ?string $databaseValue): void
    {
        $this->assertSame($phpValue, $this->fixture->convertToPHPValue($databaseValue, $this->platform));
    }

    /**
     * @return array<string, array{phpValue: string|null, databaseValue: string|null}>
     */
    public static function provideValidTransformations(): array
    {
        return [
            'null' => [
                'phpValue' => null,
                'databaseValue' => null,
            ],
            'single word' => [
                'phpValue' => 'Earth',
                'databaseValue' => 'Earth',
            ],
            'conjunction' => [
                'phpValue' => 'Earth & Moon',
                'databaseValue' => 'Earth & Moon',
            ],
            'conjunction without spaces' => [
                'phpValue' => 'Earth&Moon',
                'databaseValue' => 'Earth&Moon',
            ],
            'disjunction' => [
                'phpValue' => 'Earth | Asia',
                'databaseValue' => 'Earth | Asia',
            ],
            'negation' => [
                'phpValue' => '!Transportation',
                'databaseValue' => '!Transportation',
            ],
            'parenthesised expression' => [
                'phpValue' => '(Earth | Asia) & Moon',
                'databaseValue' => '(Earth | Asia) & Moon',
            ],
            'nested parentheses' => [
                'phpValue' => '((a|b)&(c|d))',
                'databaseValue' => '((a|b)&(c|d))',
            ],
            'word modifiers' => [
                'phpValue' => 'Moon*@%',
                'databaseValue' => 'Moon*@%',
            ],
            'full featured query' => [
                'phpValue' => 'Earth & Moon*@ & !Transportation',
                'databaseValue' => 'Earth & Moon*@ & !Transportation',
            ],
            'hyphenated and underscored words' => [
                'phpValue' => 'a-b & c_d',
                'databaseValue' => 'a-b & c_d',
            ],
            'unicode words' => [
                'phpValue' => 'Ünïcödé & 日本',
                'databaseValue' => 'Ünïcödé & 日本',
            ],
            'surrounding spaces' => [
                'phpValue' => '  a  &  b  ',
                'databaseValue' => '  a  &  b  ',
            ],
        ];
    }

    #[DataProvider('provideInvalidDatabaseValueInputs')]
    #[Test]
    public function throws_exception_for_invalid_database_value_inputs(mixed $value): void
    {
        $this->expectException(InvalidLtxtqueryForDatabaseException::class);

        $this->fixture->convertToDatabaseValue($value, $this->platform);
    }

    /**
     * @return array<string, array{mixed}>
     */
    public static function provideInvalidDatabaseValueInputs(): array
    {
        return [
            'integer input' => [42],
            'float input' => [3.14],
            'array input' => [['not', 'a', 'string']],
            'boolean input' => [true],
            'object input' => [new \stdClass()],
            'empty string' => [''],
            'dotted path' => ['a.b'],
            'words without an operator' => ['a b'],
            'dangling operator' => ['a &'],
            'leading operator' => ['& a'],
            'unbalanced opening parenthesis' => ['(a'],
            'unbalanced closing parenthesis' => ['a)'],
            'empty parentheses' => ['()'],
            'repeated operator' => ['a & & b'],
            'trailing newline' => ["Earth & Moon\n"],
            'tab separator' => ["Earth\t& Moon"],
            'star without a word' => ['*'],
            'negation without a word' => ['!'],
            'detached modifier' => ['a @'],
            'trailing negation' => ['a!'],
            'tab as separator' => ["a\t&\tb"],
            'newline as separator' => ["a\n&\nb"],
            'unsupported character' => ["a'b"],
        ];
    }

    #[DataProvider('provideInvalidPHPValueInputs')]
    #[Test]
    public function throws_exception_for_invalid_php_value_inputs(mixed $value): void
    {
        $this->expectException(InvalidLtxtqueryForPHPException::class);

        $this->fixture->convertToPHPValue($value, $this->platform);
    }

    /**
     * @return array<string, array{mixed}>
     */
    public static function provideInvalidPHPValueInputs(): array
    {
        return [
            'integer input' => [42],
            'float input' => [3.14],
            'array input' => [['not', 'a', 'string']],
            'boolean input' => [true],
            'object input' => [new \stdClass()],
        ];
    }

    #[Test]
    public function preserves_database_value_unrecognized_by_write_side_validation(): void
    {
        $this->assertSame('{unrecognized by the write-side pattern}', $this->fixture->convertToPHPValue('{unrecognized by the write-side pattern}', $this->platform));
    }
}
