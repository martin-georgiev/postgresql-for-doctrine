<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\DBAL\Types;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

class TsvectorTypeTest extends ScalarTypeTestCase
{
    protected function getTypeName(): string
    {
        return 'tsvector';
    }

    protected function getPostgresTypeName(): string
    {
        return 'TSVECTOR';
    }

    #[DataProvider('provideValidTransformations')]
    #[Test]
    public function can_transform_from_php_value(string $testValue): void
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
}
