<?php

declare(strict_types=1);

namespace Tests\MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use MartinGeorgiev\Doctrine\DBAL\Types\Jsonb;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class JsonbTest extends TestCase
{
    /**
     * @var AbstractPlatform&MockObject
     */
    private MockObject $platform;

    /**
     * @var Jsonb&MockObject
     */
    private MockObject $fixture;

    protected function setUp(): void
    {
        $this->platform = $this->createMock(AbstractPlatform::class);

        $this->fixture = $this->getMockBuilder(Jsonb::class)
            ->setMethods(null)
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * @return array<int, array<string, array|bool|float|int|string|null>>
     */
    public function validTransformations(): array
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
                'phpValue' => 13,
                'postgresValue' => '13',
            ],
            [
                'phpValue' => 13.93,
                'postgresValue' => '13.93',
            ],
            [
                'phpValue' => 'a string value',
                'postgresValue' => '"a string value"',
            ],
            [
                'phpValue' => true,
                'postgresValue' => 'true',
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
    public function has_name(): void
    {
        $this->assertEquals('jsonb', $this->fixture->getName());
    }

    /**
     * @test
     *
     * @dataProvider validTransformations
     *
     * @param array|float|int|string|null $phpValue
     */
    public function can_transform_from_php_value($phpValue, ?string $postgresValue): void
    {
        $this->assertEquals($postgresValue, $this->fixture->convertToDatabaseValue($phpValue, $this->platform));
    }

    /**
     * @test
     *
     * @dataProvider validTransformations
     *
     * @param array|float|int|string|null $phpValue
     */
    public function can_transform_to_php_value($phpValue, ?string $postgresValue): void
    {
        $this->assertEquals($phpValue, $this->fixture->convertToPHPValue($postgresValue, $this->platform));
    }
}
