<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use MartinGeorgiev\Doctrine\DBAL\Types\TextArray;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class TextArrayTest extends TestCase
{
    /**
     * @var AbstractPlatform&MockObject
     */
    private MockObject $platform;

    private TextArray $fixture;

    protected function setUp(): void
    {
        $this->platform = $this->createMock(AbstractPlatform::class);

        $this->fixture = new TextArray();
    }

    /**
     * @test
     */
    public function has_name(): void
    {
        self::assertEquals('text[]', $this->fixture->getName());
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
                'phpValue' => ['\single-back-slash-at-the-start-and-end\\'],
                'postgresValue' => '{"\\\single-back-slash-at-the-start-and-end\\\"}',
            ],
            [
                'phpValue' => ['double-back-slash-at-the-end\\\\'],
                'postgresValue' => '{"double-back-slash-at-the-end\\\\\\\"}',
            ],
            [
                'phpValue' => ['triple-\\\\\-back-slash-in-the-middle'],
                'postgresValue' => '{"triple-\\\\\\\\\\\-back-slash-in-the-middle"}',
            ],
            [
                'phpValue' => ['quadruple-back-slash\\\\\\\\'],
                'postgresValue' => '{"quadruple-back-slash\\\\\\\\\\\\\\\"}',
            ],
            [
                'phpValue' => [
                    1,
                    '2',
                    3.4,
                    '5.6',
                    'text',
                    'some text here',
                    'and some here',
                    <<<'END'
''"quotes"'' ain't no """worry""", '''right''' Alexander O'Vechkin?
END,
                    'and "double-quotes"',
                ],
                'postgresValue' => <<<'END'
{1,"2",3.4,"5.6","text","some text here","and some here","''\"quotes\"'' ain't no \"\"\"worry\"\"\", '''right''' Alexander O'Vechkin?","and \"double-quotes\""}
END,
            ],
            [
                'phpValue' => ['STRING_A', 'STRING_B', 'STRING_C', 'STRING_D'],
                'postgresValue' => '{"STRING_A","STRING_B","STRING_C","STRING_D"}',
            ],
        ];
    }

    /**
     * @test
     */
    public function can_transform_unquoted_postgres_array_to_php(): void
    {
        $postgresValue = '{STRING_A,STRING_B,STRING_C,STRING_D}';
        $expectedPhpValue = ['STRING_A', 'STRING_B', 'STRING_C', 'STRING_D'];

        self::assertEquals($expectedPhpValue, $this->fixture->convertToPHPValue($postgresValue, $this->platform));
    }

    /**
     * @test
     */
    public function can_handle_backslashes_correctly(): void
    {
        $postgresValue = '{"simple\\\backslash"}';
        $expectedPhpValue = ['simple\backslash'];

        self::assertEquals($expectedPhpValue, $this->fixture->convertToPHPValue($postgresValue, $this->platform));
    }
}
