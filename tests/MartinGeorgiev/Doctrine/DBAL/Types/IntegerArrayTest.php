<?php

declare(strict_types=1);

namespace Tests\MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Types\IntegerArray;

class IntegerArrayTest extends BaseIntegerArrayTest
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->fixture = $this->getMockBuilder(IntegerArray::class)
            ->setMethods(null)
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * @test
     */
    public function has_name(): void
    {
        $this->assertEquals('integer[]', $this->fixture->getName());
    }

    /**
     * @return array<int, array<int, mixed>>
     */
    public function invalidTransformations(): array
    {
        return \array_merge(parent::invalidTransformations(), [['-2147483647.01'], [2147483649]]);
    }

    public function validTransformations(): array
    {
        return [
            [
                'phpValue' => -2147483648,
                'postgresValue' => '-2147483648',
            ],
            [
                'phpValue' => 2147483647,
                'postgresValue' => '2147483647',
            ],
        ];
    }
}
