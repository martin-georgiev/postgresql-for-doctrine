<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsTexts;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\XmlPi;

final class XmlPiTest extends BaseVariadicFunctionTestCase
{
    protected function createFixture(): XmlPi
    {
        return new XmlPi('XMLPI');
    }

    protected function getStringFunctions(): array
    {
        return [
            'XMLPI' => XmlPi::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'creates processing instruction from one text field' => 'SELECT xmlpi(c0_.text1) AS sclr_0 FROM ContainsTexts c0_',
            'creates processing instruction from two text fields' => 'SELECT xmlpi(c0_.text1, c0_.text2) AS sclr_0 FROM ContainsTexts c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'creates processing instruction from one text field' => \sprintf('SELECT XMLPI(e.text1) FROM %s e', ContainsTexts::class),
            'creates processing instruction from two text fields' => \sprintf('SELECT XMLPI(e.text1, e.text2) FROM %s e', ContainsTexts::class),
        ];
    }
}
