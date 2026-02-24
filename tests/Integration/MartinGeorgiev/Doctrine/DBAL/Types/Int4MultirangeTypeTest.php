<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Int4Multirange as Int4MultirangeVO;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Int4Range;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

class Int4MultirangeTypeTest extends MultirangeTypeTestCase
{
    protected function getTypeName(): string
    {
        return 'int4multirange';
    }

    protected function getPostgresTypeName(): string
    {
        return 'INT4MULTIRANGE';
    }

    #[DataProvider('provideValidTransformations')]
    #[Test]
    public function can_transform_multirange_value(Int4MultirangeVO $int4MultirangeVO): void
    {
        $typeName = $this->getTypeName();
        $columnType = $this->getPostgresTypeName();

        $this->runDbalBindingRoundTrip($typeName, $columnType, $int4MultirangeVO);
    }

    /**
     * @return array<string, array{Int4MultirangeVO}>
     */
    public static function provideValidTransformations(): array
    {
        return [
            'empty multirange' => [new Int4MultirangeVO([])],
            'single range' => [new Int4MultirangeVO([new Int4Range(1, 10)])],
            'two non-overlapping ranges' => [
                new Int4MultirangeVO([new Int4Range(1, 5), new Int4Range(10, 20)]),
            ],
            'range with exclusive lower bound' => [
                new Int4MultirangeVO([new Int4Range(1, 10, false, false)]),
            ],
        ];
    }
}
