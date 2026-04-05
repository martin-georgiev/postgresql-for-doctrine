<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidXmlArrayItemForDatabaseException;
use PHPUnit\Framework\Attributes\Test;

class XmlArrayTypeTest extends ArrayTypeTestCase
{
    protected function getTypeName(): string
    {
        return 'xml[]';
    }

    protected function getPostgresTypeName(): string
    {
        return 'XML[]';
    }

    /**
     * @return array<string, array{string, array<int, string|null>}>
     */
    public static function provideValidTransformations(): array
    {
        return [
            'self-closing element' => ['self-closing element', ['<root/>']],
            'element with content' => ['element with content', ['<item>value</item>']],
            'multiple xml elements' => ['multiple xml elements', ['<a/>', '<b/>', '<c/>']],
            'nested xml' => ['nested xml', ['<root><child>text</child></root>']],
            'xml with namespace' => ['xml with namespace', ['<ns:root xmlns:ns="http://example.com"/>']],
            'xml array with null item' => ['xml array with null item', ['<a/>', null, '<b/>']],
            'empty xml array' => ['empty xml array', []],
        ];
    }

    #[Test]
    public function rejects_malformed_xml(): void
    {
        $this->expectException(InvalidXmlArrayItemForDatabaseException::class);

        $this->runDbalBindingRoundTrip($this->getTypeName(), $this->getPostgresTypeName(), ['<unclosed>']);
    }

    #[Test]
    public function rejects_non_xml_string(): void
    {
        $this->expectException(InvalidXmlArrayItemForDatabaseException::class);

        $this->runDbalBindingRoundTrip($this->getTypeName(), $this->getPostgresTypeName(), ['just plain text']);
    }
}
