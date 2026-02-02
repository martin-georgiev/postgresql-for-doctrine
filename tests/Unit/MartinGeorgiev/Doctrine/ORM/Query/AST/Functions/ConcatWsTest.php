<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsTexts;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ConcatWs;

class ConcatWsTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'CONCAT_WS' => ConcatWs::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'concatenates with separator' => "SELECT concat_ws('-', c0_.text1, c0_.text2) AS sclr_0 FROM ContainsTexts c0_",
            'concatenates multiple values' => "SELECT concat_ws(' ', c0_.text1, 'extra', c0_.text2) AS sclr_0 FROM ContainsTexts c0_",
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'concatenates with separator' => \sprintf("SELECT CONCAT_WS('-', e.text1, e.text2) FROM %s e", ContainsTexts::class),
            'concatenates multiple values' => \sprintf("SELECT CONCAT_WS(' ', e.text1, 'extra', e.text2) FROM %s e", ContainsTexts::class),
        ];
    }
}
