<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidXmlForDatabaseException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

class XmlTypeTest extends ScalarTypeTestCase
{
    protected function getTypeName(): string
    {
        return 'xml';
    }

    protected function getPostgresTypeName(): string
    {
        return 'XML';
    }

    #[DataProvider('provideValidTransformations')]
    #[Test]
    public function can_transform_from_php_value(string $testValue): void
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
            'xml declaration with root' => ['<?xml version="1.0"?><root/>'],
            'element with attributes' => ['<root id="1"><item key="value"/></root>'],
            'element with namespace' => ['<root xmlns="http://example.com"><child/></root>'],
            'element with CDATA section' => ['<root><![CDATA[some <raw> text]]></root>'],
        ];
    }

    #[Test]
    public function rejects_malformed_xml_before_database_write(): void
    {
        $this->expectException(InvalidXmlForDatabaseException::class);

        $typeName = $this->getTypeName();
        $columnType = $this->getPostgresTypeName();

        $this->runDbalBindingRoundTrip($typeName, $columnType, '<unclosed>');
    }
}
