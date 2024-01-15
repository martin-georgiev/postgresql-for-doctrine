<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsJsons;
use Tests\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\TestCase;

class TheRightExistsInTheLeftTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'RIGHT_EXISTS_IN_LEFT' => TheRightExistsInTheLeft::class,
            'ARRAY' => Arr::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            "SELECT (c0_.object1 ?? ARRAY['test']) AS sclr_0 FROM ContainsJsons c0_",
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            \sprintf("SELECT RIGHT_EXISTS_IN_LEFT(e.object1, ARRAY('test')) FROM %s e", ContainsJsons::class),
        ];
    }
}
