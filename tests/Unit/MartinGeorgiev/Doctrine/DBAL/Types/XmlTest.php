<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidXmlForDatabaseException;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidXmlForPHPException;
use MartinGeorgiev\Doctrine\DBAL\Types\Xml;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class XmlTest extends TestCase
{
    /**
     * @var AbstractPlatform&MockObject
     */
    private MockObject $platform;

    private Xml $fixture;

    protected function setUp(): void
    {
        $this->platform = $this->createMock(AbstractPlatform::class);
        $this->fixture = new Xml();
    }

    #[Test]
    public function has_name(): void
    {
        $this->assertSame('xml', $this->fixture->getName());
    }

    #[Test]
    public function converts_null_to_database_value(): void
    {
        $this->assertNull($this->fixture->convertToDatabaseValue(null, $this->platform));
    }

    #[Test]
    public function converts_null_to_php_value(): void
    {
        $this->assertNull($this->fixture->convertToPHPValue(null, $this->platform));
    }

    #[Test]
    public function converts_empty_string_from_database_to_null(): void
    {
        $this->assertNull($this->fixture->convertToPHPValue('', $this->platform));
    }

    /**
     * @return array<string, array{string}>
     */
    public static function provideValidXmlStrings(): array
    {
        return [
            'self-closing root element' => ['<root/>'],
            'nested elements' => ['<root><child>text</child></root>'],
            'xml declaration with root' => ['<?xml version="1.0"?><root/>'],
            'element with attributes' => ['<root id="1" class="main"><item key="value"/></root>'],
        ];
    }

    #[DataProvider('provideValidXmlStrings')]
    #[Test]
    public function can_round_trip_valid_xml_strings(string $xml): void
    {
        $this->assertSame($xml, $this->fixture->convertToDatabaseValue($xml, $this->platform));
        $this->assertSame($xml, $this->fixture->convertToPHPValue($xml, $this->platform));
    }

    /**
     * @return array<string, array{mixed}>
     */
    public static function provideInvalidPHPValueInputs(): array
    {
        return [
            'integer input' => [42],
            'float input' => [3.14],
            'array input' => [['not', 'a', 'string']],
            'object input' => [new \stdClass()],
            'boolean input' => [true],
        ];
    }

    #[DataProvider('provideInvalidPHPValueInputs')]
    #[Test]
    public function throws_exception_for_invalid_php_value_inputs(mixed $value): void
    {
        $this->expectException(InvalidXmlForPHPException::class);

        $this->fixture->convertToPHPValue($value, $this->platform);
    }

    /**
     * @return array<string, array{mixed}>
     */
    public static function provideInvalidDatabaseValueInputs(): array
    {
        return [
            'integer input' => [42],
            'array input' => [['not', 'a', 'string']],
        ];
    }

    #[DataProvider('provideInvalidDatabaseValueInputs')]
    #[Test]
    public function throws_exception_for_invalid_database_value_inputs(mixed $value): void
    {
        $this->expectException(InvalidXmlForDatabaseException::class);

        $this->fixture->convertToDatabaseValue($value, $this->platform);
    }
}
