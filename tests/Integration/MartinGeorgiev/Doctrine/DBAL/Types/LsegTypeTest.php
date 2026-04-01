<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Lseg as LsegValueObject;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

final class LsegTypeTest extends TestCase
{
    protected function getTypeName(): string
    {
        return 'lseg';
    }

    protected function getPostgresTypeName(): string
    {
        return 'LSEG';
    }

    #[Test]
    public function can_handle_null_values(): void
    {
        $this->runDbalBindingRoundTrip($this->getTypeName(), $this->getPostgresTypeName(), null);
    }

    #[DataProvider('provideValidTransformations')]
    #[Test]
    public function can_handle_lseg_values(string $testName, LsegValueObject $lsegValueObject): void
    {
        $this->runDbalBindingRoundTrip($this->getTypeName(), $this->getPostgresTypeName(), $lsegValueObject);
    }

    /**
     * @return array<string, array{string, LsegValueObject}>
     */
    public static function provideValidTransformations(): array
    {
        return [
            'simple segment' => ['simple segment', LsegValueObject::fromString('[(0,0),(1,1)]')],
            'segment with floats' => ['segment with floats', LsegValueObject::fromString('[(1.5,2.5),(3.5,4.5)]')],
            'segment with negative coordinates' => ['segment with negative coordinates', LsegValueObject::fromString('[(-1,-2),(-3,-4)]')],
        ];
    }
}
