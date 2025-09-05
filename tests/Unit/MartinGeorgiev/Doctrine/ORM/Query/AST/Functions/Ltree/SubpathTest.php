<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Ltree;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsTexts;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Ltree\Subpath;
use Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\TestCase;

class SubpathTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'SUBPATH' => Subpath::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'extracts subpath with offset and length' => 'SELECT subpath(c0_.text1, 0, 2) AS sclr_0 FROM ContainsTexts c0_',
            'extracts subpath with offset only' => 'SELECT subpath(c0_.text1, 1) AS sclr_0 FROM ContainsTexts c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'extracts subpath with offset and length' => \sprintf('SELECT SUBPATH(e.text1, 0, 2) FROM %s e', ContainsTexts::class),
            'extracts subpath with offset only' => \sprintf('SELECT SUBPATH(e.text1, 1) FROM %s e', ContainsTexts::class),
        ];
    }
}
