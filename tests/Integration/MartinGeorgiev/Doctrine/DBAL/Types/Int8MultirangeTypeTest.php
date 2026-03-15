<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Int8Multirange as Int8MultirangeVO;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Int8Range;
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
            'int64-only values outside int32 range' => [
                new Int8MultirangeVO([new Int8Range(2147483648, 9223372036854775807)]),
            ],
        ];
    }

    #[Test]
    public function can_normalizes_exclusive_lower_bound_to_inclusive(): void
    {
        $input = new Int8MultirangeVO([new Int8Range(1, 10, false, false)]);
        $expected = new Int8MultirangeVO([new Int8Range(2, 10)]);

        $this->runDbalBindingRoundTripExpectingDifferentRetrievedValue($this->getTypeName(), $this->getPostgresTypeName(), $input, $expected);
    }
}
