<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsTexts;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\XmlAgg;

class XmlAggTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'XML_AGG' => XmlAgg::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'basic usage' => 'SELECT xmlagg(c0_.text1) AS sclr_0 FROM ContainsTexts c0_',
            'with concatenation' => 'SELECT xmlagg(c0_.text1 || c0_.text2) AS sclr_0 FROM ContainsTexts c0_',
            'with ORDER BY' => 'SELECT xmlagg(c0_.text1 ORDER BY c0_.text1 ASC) AS sclr_0 FROM ContainsTexts c0_',
            'with ORDER BY DESC' => 'SELECT xmlagg(c0_.text1 ORDER BY c0_.text1 DESC) AS sclr_0 FROM ContainsTexts c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'basic usage' => \sprintf('SELECT XML_AGG(e.text1) FROM %s e', ContainsTexts::class),
            'with concatenation' => \sprintf('SELECT XML_AGG(CONCAT(e.text1, e.text2)) FROM %s e', ContainsTexts::class),
            'with ORDER BY' => \sprintf('SELECT XML_AGG(e.text1 ORDER BY e.text1) FROM %s e', ContainsTexts::class),
            'with ORDER BY DESC' => \sprintf('SELECT XML_AGG(e.text1 ORDER BY e.text1 DESC) FROM %s e', ContainsTexts::class),
        ];
    }
}
