<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Hstore;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsTexts;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Hstore\Skeys;
use Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\TestCase;

class SkeysTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'HSTORE_SKEYS' => Skeys::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'returns keys as set from hstore field' => 'SELECT skeys(c0_.text1) AS sclr_0 FROM ContainsTexts c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'returns keys as set from hstore field' => \sprintf('SELECT HSTORE_SKEYS(e.text1) FROM %s e', ContainsTexts::class),
        ];
    }
}
