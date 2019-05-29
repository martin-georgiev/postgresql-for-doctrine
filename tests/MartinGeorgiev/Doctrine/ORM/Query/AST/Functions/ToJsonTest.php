<?php

declare(strict_types=1);

namespace MartinGeorgiev\Tests\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ToJson;
use MartinGeorgiev\Tests\Doctrine\Fixtures\Entity\ContainsText;

class ToJsonTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'TO_JSON' => ToJson::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'SELECT to_json(c0_.text) AS sclr_0 FROM ContainsText c0_',
            'SELECT to_json(UPPER(c0_.text)) AS sclr_0 FROM ContainsText c0_',
            'SELECT to_json(1 + 1) AS sclr_0 FROM ContainsText c0_',
            'SELECT to_json(1) AS sclr_0 FROM ContainsText c0_',
            'SELECT to_json(LENGTH(c0_.text)) AS sclr_0 FROM ContainsText c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            \sprintf('SELECT TO_JSON(e.text) FROM %s e', ContainsText::class),
            \sprintf('SELECT TO_JSON(UPPER(e.text)) FROM %s e', ContainsText::class),
            \sprintf('SELECT TO_JSON(1+1) FROM %s e', ContainsText::class),
            \sprintf('SELECT TO_JSON(true) FROM %s e', ContainsText::class),
            \sprintf('SELECT TO_JSON(LENGTH(e.text)) FROM %s e', ContainsText::class),
        ];
    }
}
