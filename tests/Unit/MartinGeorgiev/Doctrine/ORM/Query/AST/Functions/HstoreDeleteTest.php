<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsTexts;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\HstoreDelete;

class HstoreDeleteTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'HSTORE_DELETE' => HstoreDelete::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'deletes key from hstore field' => 'SELECT delete(c0_.text1, c0_.text2) AS sclr_0 FROM ContainsTexts c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'deletes key from hstore field' => \sprintf('SELECT HSTORE_DELETE(e.text1, e.text2) FROM %s e', ContainsTexts::class),
        ];
    }
}
