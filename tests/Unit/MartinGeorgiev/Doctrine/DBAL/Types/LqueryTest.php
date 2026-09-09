<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidLqueryForDatabaseException;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidLqueryForPHPException;
use MartinGeorgiev\Doctrine\DBAL\Types\Lquery;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;

final class LqueryTest extends TestCase
{
    /**
     * @var AbstractPlatform&Stub
     */
    private Stub $platform;

    private Lquery $fixture;

    protected function setUp(): void
    {
        $this->platform = $this->createStub(AbstractPlatform::class);
        $this->fixture = new Lquery();
    }

    #[Test]
    public function has_name(): void
    {
        $this->assertSame('lquery', $this->fixture->getName());
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
            'single label' => [
                'phpValue' => 'Top',
                'databaseValue' => 'Top',
            ],
            'star wildcard' => [
                'phpValue' => '*',
                'databaseValue' => '*',
            ],
            'label followed by star' => [
                'phpValue' => 'Top.*',
                'databaseValue' => 'Top.*',
            ],
            'quantified star' => [
                'phpValue' => '*{1,2}',
                'databaseValue' => '*{1,2}',
            ],
            'quantified label' => [
                'phpValue' => 'tennis{1,}',
                'databaseValue' => 'tennis{1,}',
            ],
            'negated alternatives' => [
                'phpValue' => '!football|tennis',
                'databaseValue' => '!football|tennis',
            ],
            'label modifiers' => [
                'phpValue' => 'sport*@%',
                'databaseValue' => 'sport*@%',
            ],
            'full featured pattern' => [
                'phpValue' => 'Top.*{0,2}.sport*@.!football|tennis.Russ*|Spain',
                'databaseValue' => 'Top.*{0,2}.sport*@.!football|tennis.Russ*|Spain',
            ],
            'hyphenated labels' => [
                'phpValue' => 'a-b.c_d',
                'databaseValue' => 'a-b.c_d',
            ],
            'numeric labels' => [
                'phpValue' => '1.2.3',
                'databaseValue' => '1.2.3',
            ],
            'unicode labels' => [
                'phpValue' => 'äöü.日本',
                'databaseValue' => 'äöü.日本',
            ],
        ];
    }

    #[DataProvider('provideInvalidDatabaseValueInputs')]
    #[Test]
    public function throws_exception_for_invalid_database_value_inputs(mixed $value): void
    {
        $this->expectException(InvalidLqueryForDatabaseException::class);

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
            'consecutive dots' => ['Top..Child'],
            'leading dot' => ['.Top'],
            'trailing dot' => ['Top.'],
            'whitespace inside pattern' => ['a . b'],
            'space inside label' => ['a b'],
            'negated star' => ['!*'],
            'star with modifier' => ['*@'],
            'empty quantifier' => ['*{}'],
            'non-numeric quantifier' => ['*{a}'],
            'label characters after a modifier' => ['a@b'],
            'dangling alternative separator' => ['a|'],
            'negation inside alternatives' => ['a|!b'],
            'unsupported character' => ['a$b'],
            'quoted label' => ['"a b"'],
            'trailing newline' => ["Top.Sports\n"],
        ];
    }

    #[DataProvider('provideInvalidPHPValueInputs')]
    #[Test]
    public function throws_exception_for_invalid_php_value_inputs(mixed $value): void
    {
        $this->expectException(InvalidLqueryForPHPException::class);

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
