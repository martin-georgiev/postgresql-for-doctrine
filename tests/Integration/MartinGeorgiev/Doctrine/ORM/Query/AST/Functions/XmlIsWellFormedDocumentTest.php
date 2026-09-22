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
    public function returns_true_for_a_well_formed_literal(): void
    {
        $dql = "SELECT XML_IS_WELL_FORMED_DOCUMENT('<root>test</root>') as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t
                WHERE t.id = 1";

        $result = $this->executeDqlQuery($dql);
        $this->assertTrue($result[0]['result']);
    }

    #[Test]
    public function returns_false_for_an_entity_field_that_is_not_a_document(): void
    {
        $dql = 'SELECT XML_IS_WELL_FORMED_DOCUMENT(t.text1) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsTexts t
                WHERE t.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertFalse($result[0]['result']);
    }
}
