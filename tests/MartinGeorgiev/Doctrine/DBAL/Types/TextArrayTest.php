<?php

declare(strict_types=1);

namespace Tests\MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use MartinGeorgiev\Doctrine\DBAL\Types\TextArray;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class TextArrayTest extends TestCase
{
    /**
     * @var AbstractPlatform&MockObject
     */
    private AbstractPlatform $platform;

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
                    'back-slashing\double-slashing\\\hooking though',
                    'and "double-quotes"',
                ],
                'postgresValue' => <<<'END'
{1,"2",3.4,"5.6","text","some text here","and some here","''\"quotes\"'' ain't no \"\"\"worry\"\"\", '''right''' Alexander O'Vechkin?","back-slashing\\double-slashing\\\\hooking though","and \"double-quotes\""}
END,
            ],
        ];
    }
}
