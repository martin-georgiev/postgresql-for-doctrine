<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsTexts;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\XmlExists;

final class XmlExistsTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'XMLEXISTS' => XmlExists::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'tests xpath expression against xml value' => 'SELECT xmlexists(c0_.text1, c0_.text2) AS sclr_0 FROM ContainsTexts c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'tests xpath expression against xml value' => \sprintf('SELECT XMLEXISTS(e.text1, e.text2) FROM %s e', ContainsTexts::class),
        ];
    }
}
