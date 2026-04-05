<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidTsvectorArrayItemForDatabaseException;
use PHPUnit\Framework\Attributes\Test;

class TsvectorArrayTypeTest extends ArrayTypeTestCase
{
    protected function getTypeName(): string
    {
        return 'tsvector[]';
    }

    protected function getPostgresTypeName(): string
    {
        return 'TSVECTOR[]';
    }

    /**
     * @return array<string, array{array<int, string|null>}>
     */
    public static function provideValidTransformations(): array
    {
        return [
            'single lexeme' => [["'cat'"]],
            'multiple lexemes' => [["'cat' 'dog'"]],
            'lexeme with position' => [["'cat':1 'dog':2"]],
            'lexeme with weight' => [["'cat':1A 'dog':2B"]],
            'multiple tsvectors' => [["'cat' 'dog'", "'bird' 'fish'"]],
            'array with null item' => [["'cat'", null, "'dog'"]],
        ];
    }

    #[Test]
    public function rejects_empty_string_item(): void
    {
        $this->expectException(InvalidTsvectorArrayItemForDatabaseException::class);
        $this->runDbalBindingRoundTrip($this->getTypeName(), $this->getPostgresTypeName(), ['']);
    }
}
