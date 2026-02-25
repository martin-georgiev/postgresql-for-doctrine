<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Multirange;
use PHPUnit\Framework\Attributes\Test;

abstract class MultirangeTypeTestCase extends TestCase
{
    #[Test]
    public function can_handle_null_values(): void
    {
        $typeName = $this->getTypeName();
        $columnType = $this->getPostgresTypeName();

        $this->runDbalBindingRoundTrip($typeName, $columnType, null);
    }

    protected function assertTypeValueEquals(mixed $expected, mixed $actual, string $typeName): void
    {
        if (!$expected instanceof Multirange || !$actual instanceof Multirange) {
            throw new \InvalidArgumentException('assertTypeValueEquals in MultirangeTypeTestCase expects Multirange arguments.');
        }

        $this->assertSame(
            (string) $expected,
            (string) $actual,
            \sprintf('Multirange string representation mismatch for type %s', $typeName)
        );
    }
}
