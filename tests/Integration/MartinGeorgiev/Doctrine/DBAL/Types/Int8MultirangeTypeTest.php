<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Int8Multirange as Int8MultirangeVO;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Int8Range;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

class Int8MultirangeTypeTest extends MultirangeTypeTestCase
{
    protected function getTypeName(): string
    {
        return 'int8multirange';
    }

    protected function getPostgresTypeName(): string
    {
        return 'INT8MULTIRANGE';
    }

    #[DataProvider('provideValidTransformations')]
    #[Test]
    public function can_transform_multirange_value(Int8MultirangeVO $int8MultirangeVO): void
    {
        $typeName = $this->getTypeName();
        $columnType = $this->getPostgresTypeName();

        $this->runDbalBindingRoundTrip($typeName, $columnType, $int8MultirangeVO);
    }

    /**
     * @return array<string, array{Int8MultirangeVO}>
     */
    public static function provideValidTransformations(): array
    {
        return [
            'empty multirange' => [new Int8MultirangeVO([])],
            'single range' => [new Int8MultirangeVO([new Int8Range(1, 1000000000)])],
            'two non-overlapping ranges' => [
                new Int8MultirangeVO([new Int8Range(1, 1000), new Int8Range(2000, 3000)]),
            ],
        ];
    }
}
