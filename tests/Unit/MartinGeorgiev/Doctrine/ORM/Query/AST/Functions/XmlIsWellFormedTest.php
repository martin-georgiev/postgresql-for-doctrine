<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsTexts;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\XmlIsWellFormed;

final class XmlIsWellFormedTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'XML_IS_WELL_FORMED' => XmlIsWellFormed::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'checks well-formedness for a text field' => 'SELECT xml_is_well_formed(c0_.text1) AS sclr_0 FROM ContainsTexts c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'checks well-formedness for a text field' => \sprintf('SELECT XML_IS_WELL_FORMED(e.text1) FROM %s e', ContainsTexts::class),
        ];
    }
}
