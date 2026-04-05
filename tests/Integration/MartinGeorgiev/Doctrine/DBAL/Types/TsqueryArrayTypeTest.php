<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidTsqueryArrayItemForDatabaseException;
use PHPUnit\Framework\Attributes\Test;

class TsqueryArrayTypeTest extends ArrayTypeTestCase
{
    protected function getTypeName(): string
    {
        return 'tsquery[]';
    }

    protected function getPostgresTypeName(): string
    {
        return 'TSQUERY[]';
    }

    /**
     * @return array<string, array{array<int, string|null>}>
     */
    public static function provideValidTransformations(): array
    {
        return [
            'single term' => [["'cat'"]],
            'AND query' => [["'fat' & 'cat'"]],
            'OR query' => [["'cat' | 'dog'"]],
            'NOT query' => [["!'cat'"]],
            'complex query' => [["'fat' & ( 'cat' | 'rat' )"]],
            'multiple queries' => [["'cat'", "'dog' & 'bone'", "'fish' | 'bird'"]],
            'phrase search' => [["'quick' <-> 'fox'"]],
            'array with null item' => [["'cat'", null, "'dog'"]],
        ];
    }

    #[Test]
    public function can_handle_invalid_tsquery_format(): void
    {
        $this->expectException(InvalidTsqueryArrayItemForDatabaseException::class);

        $typeName = $this->getTypeName();
        $columnType = $this->getPostgresTypeName();

        $this->runDbalBindingRoundTrip($typeName, $columnType, ['']);
    }
}
