<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Ltree;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsTexts;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Ltree\Ltree2text;
use Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\TestCase;

class Ltree2textTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'LTREE2TEXT' => Ltree2text::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'casts ltree to text' => 'SELECT ltree2text(c0_.text1) AS sclr_0 FROM ContainsTexts c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'casts ltree to text' => \sprintf('SELECT LTREE2TEXT(e.text1) FROM %s e', ContainsTexts::class),
        ];
    }
}
