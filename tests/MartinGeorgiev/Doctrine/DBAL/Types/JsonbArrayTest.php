<?php

namespace MartinGeorgiev\Tests\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use MartinGeorgiev\Doctrine\DBAL\Types\JsonbArray;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class JsonbArrayTest extends TestCase
{
    /**
     * @var AbstractPlatform|MockObject
     */
    private $platform;

    /**
     * @var JsonbArray|MockObject
     */
    private $fixture;

    protected function setUp()
    {
        $this->platform = $this->createMock(AbstractPlatform::class);

        $this->fixture = $this->getMockBuilder(JsonbArray::class)
            ->setMethods(null)
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * @return array
     */
    private function getTestData()
    {
        return [
            [
                'phpValue' => null,
                'postgresJsonb' => null,
            ],
            [
                'phpValue' => [
                    [
                        'key1' => 'value1',
                        'key2' => false,
                        'key3' => '15',
                        'key4' => 15,
                        'key5' => [112, 242, 309, 310],
                    ],
                    [
                        'key1' => 'value2',
                        'key2' => true,
                        'key3' => '115',
                        'key4' => 115,
                        'key5' => [304, 404, 504, 604],
                    ],
                ],
                'postgresJsonb' => '{{"key1":"value1","key2":false,"key3":"15","key4":15,"key5":[112,242,309,310]},{"key1":"value2","key2":true,"key3":"115","key4":115,"key5":[304,404,504,604]}}',
            ],
        ];
    }

    /**
     * @test
     */
    public function has_name()
    {
        $this->assertEquals('jsonb[]', $this->fixture->getName());
    }

    /**
     * @test
     */
    public function CanTransformPhpValueToPostgresJson()
    {
        foreach ($this->getTestData() as $testData) {
            $this->assertEquals($testData['postgresJsonb'], $this->fixture->convertToDatabaseValue($testData['phpValue'], $this->platform));
        }
    }

    /**
     * @test
     */
    public function CanTransformPostgresJsonToPhpValue()
    {
        foreach ($this->getTestData() as $testData) {
            $this->assertEquals($testData['phpValue'], $this->fixture->convertToPHPValue($testData['postgresJsonb'], $this->platform));
        }
    }
}
