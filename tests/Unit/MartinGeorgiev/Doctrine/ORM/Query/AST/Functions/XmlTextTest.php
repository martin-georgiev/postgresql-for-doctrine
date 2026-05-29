<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsTexts;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\XmlText;

final class XmlTextTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'XMLTEXT' => XmlText::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'creates xml text node from a text field' => 'SELECT xmltext(c0_.text1) AS sclr_0 FROM ContainsTexts c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'creates xml text node from a text field' => \sprintf('SELECT XMLTEXT(e.text1) FROM %s e', ContainsTexts::class),
        ];
    }
}
