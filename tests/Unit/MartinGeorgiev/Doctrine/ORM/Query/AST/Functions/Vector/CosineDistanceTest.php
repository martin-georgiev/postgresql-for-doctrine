<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Vector;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsTexts;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Vector\CosineDistance;
use Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\TestCase;

class CosineDistanceTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'COSINE_DISTANCE' => CosineDistance::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'calculates cosine distance between two column vectors' => 'SELECT cosine_distance(c0_.text1, c0_.text2) AS sclr_0 FROM ContainsTexts c0_',
            'calculates cosine distance between column and literal vector' => "SELECT cosine_distance(c0_.text1, '[1,2,3]') AS sclr_0 FROM ContainsTexts c0_",
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'calculates cosine distance between two column vectors' => \sprintf('SELECT COSINE_DISTANCE(e.text1, e.text2) FROM %s e', ContainsTexts::class),
            'calculates cosine distance between column and literal vector' => \sprintf("SELECT COSINE_DISTANCE(e.text1, '[1,2,3]') FROM %s e", ContainsTexts::class),
        ];
    }
}
