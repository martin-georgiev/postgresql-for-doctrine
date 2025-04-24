<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsJsons;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\DeleteAtPath;

class DeleteAtPathTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'DELETE_AT_PATH' => DeleteAtPath::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'SELECT (c0_.object1 #- c0_.object2) AS sclr_0 FROM ContainsJsons c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            \sprintf('SELECT DELETE_AT_PATH(e.object1, e.object2) FROM %s e', ContainsJsons::class),
        ];
    }
}
