<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Hstore;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsTexts;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Hstore\HstoreSvals;
use Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\TestCase;

class HstoreSvalsTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'HSTORE_SVALS' => HstoreSvals::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'returns values as set from hstore field' => 'SELECT svals(c0_.text1) AS sclr_0 FROM ContainsTexts c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'returns values as set from hstore field' => \sprintf('SELECT HSTORE_SVALS(e.text1) FROM %s e', ContainsTexts::class),
        ];
    }
}
