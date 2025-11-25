<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use MartinGeorgiev\Doctrine\DBAL\Types\TextArray;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
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

    #[Test]
    public function has_name(): void
    {
        $this->assertEquals('text[]', $this->fixture->getName());
    }

    #[DataProvider('provideValidTransformations')]
    #[Test]
    public function can_transform_from_php_value(?array $phpValue, ?string $postgresValue): void
    {
        $this->assertEquals($postgresValue, $this->fixture->convertToDatabaseValue($phpValue, $this->platform));
    }

    #[DataProvider('provideValidTransformations')]
    #[Test]
    public function can_transform_to_php_value(?array $phpValue, ?string $postgresValue): void
    {
        $this->assertEquals($phpValue, $this->fixture->convertToPHPValue($postgresValue, $this->platform));
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

    #[Test]
    public function can_transform_unquoted_postgres_array_to_php(): void
    {
        $postgresValue = '{STRING_A,STRING_B,STRING_C,STRING_D}';
        $expectedPhpValue = ['STRING_A', 'STRING_B', 'STRING_C', 'STRING_D'];

        $this->assertEquals($expectedPhpValue, $this->fixture->convertToPHPValue($postgresValue, $this->platform));
    }

    #[Test]
    public function can_handle_backslashes_correctly(): void
    {
        $postgresValue = '{"simple\\\backslash"}';
        $expectedPhpValue = ['simple\backslash'];

        $this->assertEquals($expectedPhpValue, $this->fixture->convertToPHPValue($postgresValue, $this->platform));
    }

    #[DataProvider('provideGithubIssue424TestCases')]
    #[Test]
    public function can_preserve_string_types_retrieved_from_database_for_github_issue_424(string $testName, string $postgresValue, array $expectedResult): void
    {
        $result = $this->fixture->convertToPHPValue($postgresValue, $this->platform);

        $this->assertSame($expectedResult, $result, $testName);

        // Verify all values are strings
        foreach ($result as $value) {
            $this->assertIsString($value, $testName.' - all values should be strings');
        }
    }

    /**
     * @return list<array{
     *     testName: string,
     *     postgresValue: string,
     *     expectedResult: array{string}
     * }>
     */
    public static function provideGithubIssue424TestCases(): array
    {
        return [
            [
                'testName' => 'Numeric values should be preserved as strings',
                'postgresValue' => '{1,test}',
                'expectedResult' => ['1', 'test'],
            ],
            [
                'testName' => 'Mixed values should be preserved as strings',
                'postgresValue' => '{1,2.5,3.14,test,true,false}',
                'expectedResult' => ['1', '2.5', '3.14', 'test', 'true', 'false'],
            ],
            [
                'testName' => 'Boolean-like values should be converted to strings',
                'postgresValue' => '{true,false}',
                'expectedResult' => ['true', 'false'],
            ],
            [
                'testName' => 'Quoted boolean-like values should remain as strings',
                'postgresValue' => '{"true","false","t","f"}',
                'expectedResult' => ['true', 'false', 't', 'f'],
            ],
            [
                'testName' => 'Mixed quoted/unquoted values should all be strings',
                'postgresValue' => '{1,"2",true,"false",3.14,"test"}',
                'expectedResult' => ['1', '2', 'true', 'false', '3.14', 'test'],
            ],
            [
                'testName' => 'Null values should be converted to strings',
                'postgresValue' => '{"",null,"null","NULL"}',
                'expectedResult' => ['', 'null', 'null', 'NULL'],
            ],
        ];
    }

    #[Test]
    public function can_preserve_trailing_zeros_in_strings_that_look_like_decimals(): void
    {
        $postgresValue = '{42.00,123.50,0.00,999.99,502.00,505.00}';
        $expectedResult = ['42.00', '123.50', '0.00', '999.99', '502.00', '505.00'];

        $result = $this->fixture->convertToPHPValue($postgresValue, $this->platform);

        $this->assertSame($expectedResult, $result, 'Trailing zeros in decimal strings should be preserved');

        foreach ($result as $value) {
            $this->assertIsString($value, \sprintf('All values in text[] should be strings, but %s is not', $value));
        }
    }
}
