<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Vector;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsVectors;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Vector\L2Distance;
use Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\TestCase;

class L2DistanceTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'L2_DISTANCE' => L2Distance::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'calculates l2 distance between two column vectors' => 'SELECT l2_distance(c0_.vector1, c0_.vector2) AS sclr_0 FROM ContainsVectors c0_',
            'calculates l2 distance between column and literal vector' => "SELECT l2_distance(c0_.vector1, '[1,2,3]') AS sclr_0 FROM ContainsVectors c0_",
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'calculates l2 distance between two column vectors' => \sprintf('SELECT L2_DISTANCE(e.vector1, e.vector2) FROM %s e', ContainsVectors::class),
            'calculates l2 distance between column and literal vector' => \sprintf("SELECT L2_DISTANCE(e.vector1, '[1,2,3]') FROM %s e", ContainsVectors::class),
        ];
    }
}
