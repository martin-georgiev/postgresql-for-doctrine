<?php

declare(strict_types=1);

namespace Tests\MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidPointArrayItemForPHPException;
use MartinGeorgiev\Doctrine\DBAL\Types\PointArray;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Point;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class PointArrayTest extends TestCase
{
    /**
     * @var AbstractPlatform&MockObject
     */
    private AbstractPlatform $platform;

    private PointArray $fixture;

    protected function setUp(): void
    {
        $this->platform = $this->createMock(AbstractPlatform::class);
        $this->fixture = new PointArray();
    }

    /**
     * @test
     */
    public function has_name(): void
    {
        self::assertEquals('point[]', $this->fixture->getName());
    }

    /**
     * @test
     *
     * @dataProvider provideValidTransformations
     */
    public function can_transform_from_php_value(?array $phpValue, ?string $postgresValue): void
    {
        self::assertEquals($postgresValue, $this->fixture->convertToDatabaseValue($phpValue, $this->platform));
    }

    /**
     * @test
     *
     * @dataProvider provideValidTransformations
     */
    public function can_transform_to_php_value(?array $phpValue, ?string $postgresValue): void
    {
        self::assertEquals($phpValue, $this->fixture->convertToPHPValue($postgresValue, $this->platform));
    }

    /**
     * @return array<string, array{phpValue: array<Point>|null, postgresValue: string|null}>
     */
    public static function provideValidTransformations(): array
    {
        return [
            'null' => [
                'phpValue' => null,
                'postgresValue' => null,
            ],
            'empty array' => [
                'phpValue' => [],
                'postgresValue' => '{}',
            ],
            'single point' => [
                'phpValue' => [new Point(1.23, 4.56)],
                'postgresValue' => '{"(1.230000, 4.560000)"}',
            ],
            'multiple points' => [
                'phpValue' => [
                    new Point(1.23, 4.56),
                    new Point(-7.89, 0.12),
                ],
                'postgresValue' => '{"(1.230000, 4.560000)","(-7.890000, 0.120000)"}',
            ],
            'points with zero values' => [
                'phpValue' => [
                    new Point(0.0, 0.0),
                    new Point(10.5, -3.7),
                ],
                'postgresValue' => '{"(0.000000, 0.000000)","(10.500000, -3.700000)"}',
            ],
        ];
    }

    /**
     * @test
     *
     * @dataProvider provideInvalidTransformations
     */
    public function throws_exception_when_invalid_data_provided_to_convert_to_database_value(mixed $phpValue): void
    {
        $this->expectException(InvalidPointArrayItemForPHPException::class);
        $this->fixture->convertToDatabaseValue($phpValue, $this->platform); // @phpstan-ignore-line
    }

    /**
     * @return array<string, array{mixed}>
     */
    public static function provideInvalidTransformations(): array
    {
        return [
            'not an array' => ['string value'],
            'array containing non-Point items' => [[1, 2, 3]],
            'invalid nested point' => [['(1.23,4.56)']],
            'mixed array (valid and invalid points)' => [
                [
                    new Point(1.23, 4.56),
                    'invalid',
                ],
            ],
        ];
    }
}
