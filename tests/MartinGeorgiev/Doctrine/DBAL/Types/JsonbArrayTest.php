<?php

declare(strict_types=1);

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

    protected function setUp(): void
    {
        $this->platform = $this->createMock(AbstractPlatform::class);

        $this->fixture = $this->getMockBuilder(JsonbArray::class)
            ->setMethods(null)
            ->disableOriginalConstructor()
            ->getMock();
    }

    public function validTransformations(): array
    {
        return [
            [
                'phpValue' => null,
                'postgresJsonb' => null,
            ],
            [
                'phpValue' => [],
                'postgresValue' => '{}',
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
    public function has_name(): void
    {
        $this->assertEquals('jsonb[]', $this->fixture->getName());
    }

    /**
     * @test
     * @dataProvider validTransformations
     */
    public function can_transform_from_php_value(?array $phpValue, ?string $postgresValue): void
    {
        $this->assertEquals($postgresValue, $this->fixture->convertToDatabaseValue($phpValue, $this->platform));
    }

    /**
     * @test
     * @dataProvider validTransformations
     */
    public function can_transform_to_php_value(?array $phpValue, ?string $postgresValue): void
    {
        $this->assertEquals($phpValue, $this->fixture->convertToPHPValue($postgresValue, $this->platform));
    }
}
