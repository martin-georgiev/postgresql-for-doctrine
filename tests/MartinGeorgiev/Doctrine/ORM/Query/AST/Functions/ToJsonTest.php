<?php

declare(strict_types=1);

namespace Tests\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsTexts;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ToJson;

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
            'SELECT to_json(c0_.text1) AS sclr_0 FROM ContainsTexts c0_',
            'SELECT to_json(UPPER(c0_.text1)) AS sclr_0 FROM ContainsTexts c0_',
            'SELECT to_json(1 + 1) AS sclr_0 FROM ContainsTexts c0_',
            'SELECT to_json(1) AS sclr_0 FROM ContainsTexts c0_',
            'SELECT to_json(LENGTH(c0_.text1)) AS sclr_0 FROM ContainsTexts c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            \sprintf('SELECT TO_JSON(e.text1) FROM %s e', ContainsTexts::class),
            \sprintf('SELECT TO_JSON(UPPER(e.text1)) FROM %s e', ContainsTexts::class),
            \sprintf('SELECT TO_JSON(1+1) FROM %s e', ContainsTexts::class),
            \sprintf('SELECT TO_JSON(true) FROM %s e', ContainsTexts::class),
            \sprintf('SELECT TO_JSON(LENGTH(e.text1)) FROM %s e', ContainsTexts::class),
        ];
    }
}
