<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsTexts;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\XmlPi;

final class XmlPiTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'XMLPI' => XmlPi::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'creates processing instruction from target only' => 'SELECT xmlpi(NAME foo) AS sclr_0 FROM ContainsTexts c0_',
            'creates processing instruction from target and content field' => 'SELECT xmlpi(NAME foo, c0_.text2) AS sclr_0 FROM ContainsTexts c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'creates processing instruction from target only' => \sprintf("SELECT XMLPI('foo') FROM %s e", ContainsTexts::class),
            'creates processing instruction from target and content field' => \sprintf("SELECT XMLPI('foo', e.text2) FROM %s e", ContainsTexts::class),
        ];
    }
}
