<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\DBAL\Types;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

class TsqueryTypeTest extends ScalarTypeTestCase
{
    protected function getTypeName(): string
    {
        return 'tsquery';
    }

    protected function getPostgresTypeName(): string
    {
        return 'TSQUERY';
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
            'single term' => ["'cat'"],
            'AND query' => ["'fat' & 'cat'"],
            'OR query' => ["'cat' | 'dog'"],
            'NOT query' => ["!'cat'"],
            'complex query' => ["'fat' & ( 'cat' | 'rat' )"],
        ];
    }
}
