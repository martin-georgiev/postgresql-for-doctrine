<?php

declare(strict_types=1);

namespace Tests\MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Types\BigIntArray;

class BigIntArrayTest extends BaseIntegerArrayTest
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->fixture = new BigIntArray(); // @phpstan-ignore-line
    }

    /**
     * @test
     */
    public function has_name(): void
    {
        $this->assertEquals('bigint[]', $this->fixture->getName());
    }

    public static function provideInvalidTransformations(): array
    {
        return \array_merge(parent::provideInvalidTransformations(), [['-9223372036854775807.01'], [-9_223_372_036_854_775_809.0]]);
    }

    /**
     * @return list<array{
     *     phpValue: int,
     *     postgresValue: string
     * }>
     */
    public static function provideValidTransformations(): array
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
