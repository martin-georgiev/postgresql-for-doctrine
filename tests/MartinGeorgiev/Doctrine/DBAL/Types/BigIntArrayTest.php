<?php

declare(strict_types=1);

namespace Tests\MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Types\BigIntArray;

class BigIntArrayTest extends BaseIntegerArrayTest
{
    protected function setUp(): void
    {
        parent::setUp();

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
        return \array_merge(parent::invalidTransformations(), [['-9223372036854775807.01'], [-9_223_372_036_854_775_809.0]]);
    }

    public function validTransformations(): array
    {
        return [
            [
                'phpValue' => -9_223_372_036_854_775_807,
                'postgresValue' => '-9223372036854775807',
            ],
            [
                'phpValue' => 9_223_372_036_854_775_807,
                'postgresValue' => '9223372036854775807',
            ],
        ];
    }
}
