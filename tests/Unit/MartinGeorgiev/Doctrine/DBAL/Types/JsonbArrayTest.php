<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidJsonArrayItemForPHPException;
use MartinGeorgiev\Doctrine\DBAL\Types\JsonbArray;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class JsonbArrayTest extends TestCase
{
    /**
     * @var AbstractPlatform&MockObject
     */
    private MockObject $platform;

    private JsonbArray $fixture;

    protected function setUp(): void
    {
        $this->platform = $this->createMock(AbstractPlatform::class);

        $this->fixture = new JsonbArray();
    }

    #[Test]
    public function has_name(): void
    {
        self::assertEquals('jsonb[]', $this->fixture->getName());
    }

    #[DataProvider('provideValidTransformations')]
    #[Test]
    public function can_transform_from_php_value(?array $phpValue, ?string $postgresValue): void
    {
        self::assertEquals($postgresValue, $this->fixture->convertToDatabaseValue($phpValue, $this->platform));
    }

    #[DataProvider('provideValidTransformations')]
    #[Test]
    public function can_transform_to_php_value(?array $phpValue, ?string $postgresValue): void
    {
        self::assertEquals($phpValue, $this->fixture->convertToPHPValue($postgresValue, $this->platform));
    }

    /**
     * @return list<array{
     *     phpValue: array|null,
     *     postgresValue: string|null
     * }>
     */
    public static function provideValidTransformations(): array
    {
        return [
            [
                'phpValue' => null,
                'postgresValue' => null,
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
                'postgresValue' => '{{"key1":"value1","key2":false,"key3":"15","key4":15,"key5":[112,242,309,310]},{"key1":"value2","key2":true,"key3":"115","key4":115,"key5":[304,404,504,604]}}',
            ],
        ];
    }

    #[DataProvider('provideInvalidPHPValuesForDatabaseTransformation')]
    #[Test]
    public function throws_exception_when_invalid_data_provided_to_convert_to_php_value(string $postgresValue): void
    {
        $this->expectException(InvalidJsonArrayItemForPHPException::class);
        $this->expectExceptionMessage('Invalid JSON format in array');

        $this->fixture->convertToPHPValue($postgresValue, $this->platform);
    }

    /**
     * @return array<string, array{string}>
     */
    public static function provideInvalidPHPValuesForDatabaseTransformation(): array
    {
        return [
            'non-array json' => ['"a string encoded as json"'],
            'invalid json format' => ['{invalid json}'],
        ];
    }
}
