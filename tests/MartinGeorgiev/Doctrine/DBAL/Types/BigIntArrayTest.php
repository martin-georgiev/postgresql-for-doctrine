<?php

declare(strict_types=1);

namespace MartinGeorgiev\Tests\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Types\BigIntArray;

class BigIntArrayTest extends BaseIntegerArrayTest
{
    protected function setUp(): void
    {
        $this->fixture = $this->getMockBuilder(BigIntArray::class)
            ->setMethods(null)
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * @test
     */
    public function has_name(): void
    {
        $this->assertEquals('bigint[]', $this->fixture->getName());
    }

    public function invalidTransformations(): array
    {
        return \array_merge(parent::invalidTransformations(), [['-9223372036854775807.01'], [-9223372036854775809]]);
    }

    public function validTransformations(): array
    {
        return [
            [
                'phpValue' => -9223372036854775807,
                'postgresValue' => '-9223372036854775807',
            ],
            [
                'phpValue' => 9223372036854775807,
                'postgresValue' => '9223372036854775807',
            ],
        ];
    }
}
