<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Hstore;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsTexts;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Hstore\Avals;
use Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\TestCase;

class AvalsTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'HSTORE_AVALS' => Avals::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'returns values from hstore field' => 'SELECT avals(c0_.text1) AS sclr_0 FROM ContainsTexts c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'returns values from hstore field' => \sprintf('SELECT HSTORE_AVALS(e.text1) FROM %s e', ContainsTexts::class),
        ];
    }
}
