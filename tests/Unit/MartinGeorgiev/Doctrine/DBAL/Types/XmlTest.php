<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidXmlForDatabaseException;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidXmlForPHPException;
use MartinGeorgiev\Doctrine\DBAL\Types\Xml;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;

final class XmlTest extends TestCase
{
    /**
     * @var AbstractPlatform&Stub
     */
    private Stub $platform;

    private Xml $fixture;

    protected function setUp(): void
    {
        $this->platform = $this->createStub(AbstractPlatform::class);
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

    #[DataProvider('provideValidXmlStrings')]
    #[Test]
    public function roundtrips_valid_xml_strings(string $xml): void
    {
        $this->assertSame($xml, $this->fixture->convertToDatabaseValue($xml, $this->platform));
        $this->assertSame($xml, $this->fixture->convertToPHPValue($xml, $this->platform));
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
            'element with namespace' => ['<root xmlns="http://example.com"><child/></root>'],
            'element with CDATA section' => ['<root><![CDATA[some <raw> text]]></root>'],
            'empty content' => [''],
            'multiple root elements' => ['<root/><root/>'],
            'plain text' => ['not xml at all'],
            'text around an element' => ['before <root/> after'],
            'xml declaration with a fragment' => ['<?xml version="1.0"?><a/><b/>'],
            'xml declaration with plain text' => ['<?xml version="1.0" encoding="UTF-8" standalone="yes"?>text'],
            'doctype document' => ['<!DOCTYPE root><root/>'],
            'doctype document after many comments' => [\str_repeat('<!--c-->', 20000).'<!DOCTYPE root><root/>'],
            'xml declaration with an unsupported encoding before a doctype' => ['<?xml version="1.0" encoding="UTF-16"?><!DOCTYPE root><root/>'],
            'undeclared namespace prefix' => ['<ns:root/>'],
            'external network entity declaration (LIBXML_NONET blocks resolution, XML remains structurally valid)' => ['<?xml version="1.0"?><!DOCTYPE test [<!ENTITY ext SYSTEM "http://example.com">]><test>&ext;</test>'],
        ];
    }

    #[DataProvider('provideMalformedXmlStrings')]
    #[Test]
    public function throws_exception_for_malformed_xml_in_database_value(string $invalidXml): void
    {
        $this->expectException(InvalidXmlForDatabaseException::class);

        $this->fixture->convertToDatabaseValue($invalidXml, $this->platform);
    }

    /**
     * @return array<string, array{string}>
     */
    public static function provideMalformedXmlStrings(): array
    {
        return [
            'unclosed tag' => ['<root>'],
            'mismatched tags' => ['<root><child></root>'],
            'invalid tag name' => ['<123invalid/>'],
            'unescaped ampersand' => ['<root>a & b</root>'],
            'undefined entity' => ['&undefined;'],
            'xml declaration after leading whitespace' => [' <?xml version="1.0"?><root/>'],
            'malformed xml declaration' => ['<?xml?><root/>'],
            'doctype document with two root elements' => ['<!DOCTYPE root><root/><root/>'],
            'unsupported xml version before a doctype' => ['<?xml version="2.0"?><!DOCTYPE root><root/>'],
            'empty encoding before a doctype' => ['<?xml version="1.0" encoding=""?><!DOCTYPE root><root/>'],
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

    #[DataProvider('provideInvalidDatabaseValueInputs')]
    #[Test]
    public function throws_exception_for_invalid_database_value_inputs(mixed $value): void
    {
        $this->expectException(InvalidXmlForDatabaseException::class);

        $this->fixture->convertToDatabaseValue($value, $this->platform);
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
}
