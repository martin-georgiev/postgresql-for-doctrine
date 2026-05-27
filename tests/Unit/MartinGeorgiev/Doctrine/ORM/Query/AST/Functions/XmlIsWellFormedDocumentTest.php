<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsTexts;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\XmlIsWellFormedDocument;

final class XmlIsWellFormedDocumentTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'XML_IS_WELL_FORMED_DOCUMENT' => XmlIsWellFormedDocument::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'checks document well-formedness for a text field' => 'SELECT xml_is_well_formed_document(c0_.text1) AS sclr_0 FROM ContainsTexts c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'checks document well-formedness for a text field' => \sprintf('SELECT XML_IS_WELL_FORMED_DOCUMENT(e.text1) FROM %s e', ContainsTexts::class),
        ];
    }
}
