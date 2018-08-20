<?php

namespace MartinGeorgiev\Tests\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use MartinGeorgiev\Doctrine\DBAL\Types\Jsonb;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class JsonbTest extends TestCase
{
    /**
     * @var AbstractPlatform|MockObject
     */
    private $platform;

    /**
     * @var Jsonb|MockObject
     */
    private $fixture;

    protected function setUp()
    {
        $this->platform = $this->createMock(AbstractPlatform::class);

        $this->fixture = $this->getMockBuilder(Jsonb::class)
            ->setMethods(null)
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * @return array
     */
    public function validTransformations()
    {
        return [
            [
                'phpValue' => null,
                'postgresValue' => null,
            ],
            [
                'phpValue' => [],
                'postgresValue' => '[]',
            ],
            [
                'phpValue' => [681, 1185, 1878, 1989],
                'postgresValue' => '[681,1185,1878,1989]',
            ],
            [
                'phpValue' => [
                    'key1' => 'value1',
                    'key2' => false,
                    'key3' => '15',
                    'key4' => 15,
                    'key5' => [112, 242, 309, 310],
                ],
                'postgresValue' => '{"key1":"value1","key2":false,"key3":"15","key4":15,"key5":[112,242,309,310]}',
            ],
        ];
    }

    /**
     * @test
     */
    public function has_name()
    {
        $this->assertEquals('jsonb', $this->fixture->getName());
    }

    /**
     * @test
     * @dataProvider validTransformations
     *
     * @param array|null $phpValue
     * @param string|null $postgresValue
     */
    public function can_transform_from_php_value($phpValue, $postgresValue)
    {
        $this->assertEquals($postgresValue, $this->fixture->convertToDatabaseValue($phpValue, $this->platform));
    }

    /**
     * @test
     * @dataProvider validTransformations
     *
     * @param array|null $phpValue
     * @param string|null $postgresValue
     */
    public function can_transform_to_php_value($phpValue, $postgresValue)
    {
        $this->assertEquals($phpValue, $this->fixture->convertToPHPValue($postgresValue, $this->platform));
    }
}
