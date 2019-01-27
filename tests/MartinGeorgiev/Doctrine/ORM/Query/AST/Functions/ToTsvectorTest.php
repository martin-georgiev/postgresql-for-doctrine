<?php

declare(strict_types=1);

namespace MartinGeorgiev\Tests\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ToTsvector;
use MartinGeorgiev\Tests\Doctrine\Fixtures\Entity\ContainsText;

class ToTsvectorTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'TO_TSVECTOR' => ToTsvector::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'SELECT to_tsvector(c0_.text) AS sclr_0 FROM ContainsText c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            \sprintf('SELECT TO_TSVECTOR(e.text) FROM %s e', ContainsText::class),
        ];
    }
}
