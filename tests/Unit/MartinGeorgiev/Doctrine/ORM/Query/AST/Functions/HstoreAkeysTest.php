<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsTexts;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\HstoreAkeys;

class HstoreAkeysTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'HSTORE_AKEYS' => HstoreAkeys::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'returns keys from hstore field' => 'SELECT akeys(c0_.text1) AS sclr_0 FROM ContainsTexts c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'returns keys from hstore field' => \sprintf('SELECT HSTORE_AKEYS(e.text1) FROM %s e', ContainsTexts::class),
        ];
    }
}
