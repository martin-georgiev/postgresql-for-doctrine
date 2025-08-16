<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\DBAL\Types;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

class TextArrayTypeTest extends ArrayTypeTestCase
{
    protected function getTypeName(): string
    {
        return 'text[]';
    }

    protected function getPostgresTypeName(): string
    {
        return 'TEXT[]';
    }

    #[DataProvider('provideValidTransformations')]
    #[Test]
    public function can_handle_array_values(string $testName, array $arrayValue): void
    {
        parent::can_handle_array_values($testName, $arrayValue);
    }

    public static function provideValidTransformations(): array
    {
        return [
            'simple text array' => ['simple text array', ['foo', 'bar', 'baz']],
            'text array with special chars' => ['text array with special chars', ['foo"bar', 'baz\qux', 'with,comma']],
            'text array with empty strings' => ['text array with empty strings', ['', 'not empty', '']],
            'text array with unicode' => ['text array with unicode', ['café', 'naïve', 'résumé']],
            'text array with numbers as strings' => ['text array with numbers as strings', ['123', '456', '789']],
            'text array with null elements' => ['text array with null elements', ['foo', null, 'baz']],
        ];
    }
}
