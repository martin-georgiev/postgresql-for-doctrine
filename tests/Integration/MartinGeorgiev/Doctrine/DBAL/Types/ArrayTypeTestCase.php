<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\DBAL\Types;

use PHPUnit\Framework\Attributes\Test;

abstract class ArrayTypeTestCase extends TestCase
{
    #[Test]
    public function can_handle_empty_array(): void
    {
        $typeName = $this->getTypeName();
        $columnType = $this->getPostgresTypeName();

        $this->runDbalBindingRoundTrip($typeName, $columnType, []);
    }

    #[Test]
    public function can_handle_null_values(): void
    {
        $typeName = $this->getTypeName();
        $columnType = $this->getPostgresTypeName();

        $this->runDbalBindingRoundTrip($typeName, $columnType, null);
    }

    /**
     * Data-driven test for array values.
     * Subclasses should add #[DataProvider('provideValidTransformations')].
     */
    public function can_handle_array_values(string $testName, array $arrayValue): void
    {
        $typeName = $this->getTypeName();
        $columnType = $this->getPostgresTypeName();

        $this->runDbalBindingRoundTrip($typeName, $columnType, $arrayValue);
    }
}
