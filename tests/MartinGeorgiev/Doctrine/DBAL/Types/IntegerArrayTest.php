<?php

declare(strict_types=1);

namespace Tests\MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Types\IntegerArray;

class IntegerArrayTest extends BaseIntegerArrayTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->fixture = new IntegerArray(); // @phpstan-ignore-line
    }

    /**
     * @test
     */
    public function has_name(): void
    {
        self::assertEquals('integer[]', $this->fixture->getName());
    }

    /**
     * @return array<int, mixed>
     */
    public static function provideInvalidTransformations(): array
    {
        return \array_merge(parent::provideInvalidTransformations(), [['-2147483647.01'], [2_147_483_649]]);
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
                'phpValue' => -2_147_483_648,
                'postgresValue' => '-2147483648',
            ],
            [
                'phpValue' => 2_147_483_647,
                'postgresValue' => '2147483647',
            ],
        ];
    }
}
