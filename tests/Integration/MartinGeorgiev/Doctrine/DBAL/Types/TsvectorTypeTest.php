<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidTsvectorForPHPException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

final class TsvectorTypeTest extends ScalarTypeTestCase
{
    protected function getTypeName(): string
    {
        return 'tsvector';
    }

    #[DataProvider('provideValidTransformations')]
    #[Test]
    public function roundtrips_value(string $testValue): void
    {
        $typeName = $this->getTypeName();
        $columnType = $this->getPostgresTypeName();

        $this->runDbalBindingRoundTrip($typeName, $columnType, $testValue);
    }

    /**
     * @return array<string, array{string}>
     */
    public static function provideValidTransformations(): array
    {
        return [
            'single lexeme' => ["'cat'"],
            'multiple lexemes' => ["'cat' 'fat' 'rat'"],
            'lexemes with positions' => ["'cat':3 'fat':2 'rat':1"],
            'lexemes with weights' => ["'important':1A 'secondary':2B"],
        ];
    }

    #[Test]
    public function rejects_empty_string(): void
    {
        $this->expectException(InvalidTsvectorForPHPException::class);

        $typeName = $this->getTypeName();
        $columnType = $this->getPostgresTypeName();

        $this->runDbalBindingRoundTrip($typeName, $columnType, '');
    }
}
