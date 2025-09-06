<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Ltree;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsTexts;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Ltree\Subltree;
use Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\TestCase;

class SubltreeTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'SUBLTREE' => Subltree::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'extracts subpath from ltree' => 'SELECT subltree(c0_.text1, 1, 2) AS sclr_0 FROM ContainsTexts c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'extracts subpath from ltree' => \sprintf('SELECT SUBLTREE(e.text1, 1, 2) FROM %s e', ContainsTexts::class),
        ];
    }
}
