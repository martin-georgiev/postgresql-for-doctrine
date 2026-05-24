<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsTexts;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BaseVariadicFunction;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\XmlIsWellFormed;

class XmlIsWellFormedTest extends BaseVariadicFunctionTestCase
{
    protected function createFixture(): BaseVariadicFunction
    {
        return new XmlIsWellFormed('XML_IS_WELL_FORMED');
    }

    protected function getStringFunctions(): array
    {
        return [
            'XML_IS_WELL_FORMED' => XmlIsWellFormed::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'checks well-formed xml with one argument' => 'SELECT xml_is_well_formed(c0_.text1) AS sclr_0 FROM ContainsTexts c0_',
            'checks well-formed xml with two arguments' => 'SELECT xml_is_well_formed(c0_.text1, c0_.text2) AS sclr_0 FROM ContainsTexts c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'checks well-formed xml with one argument' => \sprintf('SELECT XML_IS_WELL_FORMED(e.text1) FROM %s e', ContainsTexts::class),
            'checks well-formed xml with two arguments' => \sprintf('SELECT XML_IS_WELL_FORMED(e.text1, e.text2) FROM %s e', ContainsTexts::class),
        ];
    }
}
