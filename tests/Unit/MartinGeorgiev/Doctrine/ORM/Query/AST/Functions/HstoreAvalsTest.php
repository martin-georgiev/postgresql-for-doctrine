<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsTexts;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\HstoreAvals;

class HstoreAvalsTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'HSTORE_AVALS' => HstoreAvals::class,
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
