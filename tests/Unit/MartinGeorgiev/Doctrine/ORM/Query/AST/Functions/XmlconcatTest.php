<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsTexts;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BaseVariadicFunction;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Xmlconcat;

class XmlconcatTest extends BaseVariadicFunctionTestCase
{
    protected function createFixture(): BaseVariadicFunction
    {
        return new Xmlconcat('XMLCONCAT');
    }

    protected function getStringFunctions(): array
    {
        return [
            'XMLCONCAT' => Xmlconcat::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'concatenates two xml values' => 'SELECT xmlconcat(c0_.text1, c0_.text2) AS sclr_0 FROM ContainsTexts c0_',
            'concatenates three xml values' => 'SELECT xmlconcat(c0_.text1, c0_.text2, c0_.text1) AS sclr_0 FROM ContainsTexts c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'concatenates two xml values' => \sprintf('SELECT XMLCONCAT(e.text1, e.text2) FROM %s e', ContainsTexts::class),
            'concatenates three xml values' => \sprintf('SELECT XMLCONCAT(e.text1, e.text2, e.text1) FROM %s e', ContainsTexts::class),
        ];
    }
}
