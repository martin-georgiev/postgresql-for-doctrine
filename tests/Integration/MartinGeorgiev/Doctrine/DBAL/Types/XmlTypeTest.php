<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidXmlForDatabaseException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

final class XmlTypeTest extends ScalarTypeTestCase
{
    protected function getTypeName(): string
    {
        return 'xml';
    }

    #[DataProvider('provideValidTransformations')]
    #[Test]
    public function roundtrips_value(string $testValue): void
    {
        $typeName = $this->getTypeName();
        $columnType = $this->getPostgresTypeName();

        $this->runDbalBindingRoundTrip($typeName, $columnType, $testValue);
    }

    /**
     * @return array<string, array{string}>
     */
    public static function provideValidTransformations(): array
    {
        return [
            'self-closing root element' => ['<root/>'],
            'nested elements' => ['<root><child>text</child></root>'],
            'element with attributes' => ['<root id="1"><item key="value"/></root>'],
            'element with namespace' => ['<root xmlns="http://example.com"><child/></root>'],
            'element with CDATA section' => ['<root><![CDATA[some <raw> text]]></root>'],
            // Unlike the version and encoding pseudo-attributes, standalone survives storage — see
            // normalizes_xml_declaration_on_storage() for the ones PostgreSQL drops.
            'declaration with standalone' => ['<?xml version="1.0" standalone="yes"?><root/>'],
        ];
    }

    #[DataProvider('provideDroppedXmlDeclarations')]
    #[Test]
    public function normalizes_xml_declaration_on_storage(string $storedValue, string $retrievedValue): void
    {
        $this->runDbalBindingRoundTripExpectingDifferentRetrievedValue(
            $this->getTypeName(),
            $this->getPostgresTypeName(),
            $storedValue,
            $retrievedValue
        );
    }

    /**
     * PostgreSQL drops the version and encoding pseudo-attributes on storage, but keeps standalone, which is why the
     * standalone form lives in provideValidTransformations() instead.
     *
     * @return array<string, array{storedValue: string, retrievedValue: string}>
     */
    public static function provideDroppedXmlDeclarations(): array
    {
        return [
            'version only' => [
                'storedValue' => '<?xml version="1.0"?><root/>',
                'retrievedValue' => '<root/>',
            ],
            'version and encoding' => [
                'storedValue' => '<?xml version="1.0" encoding="UTF-8"?><root/>',
                'retrievedValue' => '<root/>',
            ],
            'declaration followed by a newline' => [
                'storedValue' => "<?xml version=\"1.0\"?>\n<root/>",
                'retrievedValue' => '<root/>',
            ],
            'declaration before nested elements' => [
                'storedValue' => '<?xml version="1.0"?><root><a>1</a></root>',
                'retrievedValue' => '<root><a>1</a></root>',
            ],
        ];
    }

    #[DataProvider('provideMalformedXml')]
    #[Test]
    public function rejects_malformed_xml_before_database_write(string $malformedXml): void
    {
        $this->expectException(InvalidXmlForDatabaseException::class);

        $typeName = $this->getTypeName();
        $columnType = $this->getPostgresTypeName();

        $this->runDbalBindingRoundTrip($typeName, $columnType, $malformedXml);
    }

    /**
     * @return array<string, array{string}>
     */
    public static function provideMalformedXml(): array
    {
        return [
            'unclosed element' => ['<unclosed>'],
            'mismatched closing tag' => ['<a></b>'],
            'unclosed nested element' => ['<root><child></root>'],
            'unquoted attribute value' => ['<root attr=unquoted/>'],
            'empty tag name' => ['<>'],
            'not markup at all' => ['not xml at all'],
        ];
    }
}
