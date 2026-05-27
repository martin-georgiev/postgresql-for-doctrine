<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\XmlIsWellFormedDocument;
use PHPUnit\Framework\Attributes\Test;

final class XmlIsWellFormedDocumentTest extends TextTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'XML_IS_WELL_FORMED_DOCUMENT' => XmlIsWellFormedDocument::class,
        ];
    }

    #[Test]
    public function checks_xml_is_well_formed_document_of_literal(): void
    {
        $dql = "SELECT XML_IS_WELL_FORMED_DOCUMENT('<root>test</root>') as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t
                WHERE t.id = 1";

        $result = $this->executeDqlQuery($dql);
        $this->assertTrue($result[0]['result']);
    }

    #[Test]
    public function checks_xml_is_well_formed_document_with_entity_property(): void
    {
        $dql = 'SELECT XML_IS_WELL_FORMED_DOCUMENT(t.text1) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsTexts t
                WHERE t.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertFalse($result[0]['result']);
    }
}
