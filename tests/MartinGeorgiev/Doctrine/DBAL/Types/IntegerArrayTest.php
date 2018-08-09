<?php

namespace MartinGeorgiev\Tests\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Types\IntegerArray;

class IntegerArrayTest extends AbstractIntegerArrayTest
{
    protected function setUp()
    {
        $this->fixture = $this->getMockBuilder(IntegerArray::class)
            ->setMethods(null)
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * @test
     */
    public function has_name()
    {
        $this->assertEquals('integer[]', $this->fixture->getName());
    }

    /**
     * @return array
     */
    public function invalidTransformations()
    {
        return array_merge(parent::invalidTransformations(), [['-2147483647.01'], [2147483649]]);
    }

    /**
     * @return array
     */
    public function validTransformations()
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
