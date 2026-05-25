<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\DBAL\Types;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

final class TimetzTypeTest extends ScalarTypeTestCase
{
    protected function getTypeName(): string
    {
        return 'timetz';
    }

    #[DataProvider('provideValidTransformations')]
    #[Test]
    public function can_transform_from_php_value(string $testValue): void
    {
        $this->runDbalBindingRoundTrip($this->getTypeName(), $this->getPostgresTypeName(), $testValue);
    }

    /**
     * @return array<string, array{string}>
     */
    public static function provideValidTransformations(): array
    {
        return [
            'UTC time' => ['10:30:00+00'],
            'positive offset' => ['14:45:00+02'],
            'negative offset' => ['08:00:00-05'],
            'with microseconds' => ['12:34:56.123456+02'],
        ];
    }
}
