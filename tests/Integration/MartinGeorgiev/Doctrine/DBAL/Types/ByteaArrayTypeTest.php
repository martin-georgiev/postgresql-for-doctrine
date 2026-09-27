<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\DBAL\Types;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

final class ByteaArrayTypeTest extends ArrayTypeTestCase
{
    protected function getTypeName(): string
    {
        return 'bytea[]';
    }

    /**
     * @param array<int, string|null> $arrayValue
     */
    #[DataProvider('provideValidTransformations')]
    #[Test]
    public function roundtrips_value_read_in_the_escape_format(array $arrayValue): void
    {
        $typeName = $this->getTypeName();
        $columnType = $this->getPostgresTypeName();

        $this->connection->executeStatement("SET bytea_output = 'escape'");

        try {
            $this->runDbalBindingRoundTrip($typeName, $columnType, $arrayValue);
        } finally {
            $this->connection->executeStatement('RESET bytea_output');
        }
    }

    /**
     * @return array<string, array{array<int, string|null>}>
     */
    public static function provideValidTransformations(): array
    {
        return [
            'array of ascii strings' => [['hello', 'world']],
            'array with null item' => [['hello', null, 'world']],
            'array with binary data' => [["binary\x00data", "\xFF\xFE"]],
            'array with empty string' => [['']],
        ];
    }
}
