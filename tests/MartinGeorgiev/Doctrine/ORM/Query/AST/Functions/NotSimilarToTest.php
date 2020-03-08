<?php

declare(strict_types=1);

namespace MartinGeorgiev\Tests\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\NotSimilarTo;
use MartinGeorgiev\Tests\Doctrine\Fixtures\Entity\ContainsText;

class NotSimilarToTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'NOT_SIMILAR_TO' => NotSimilarTo::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            "SELECT c0_.text not similar to 'TEST' AS sclr_0 FROM ContainsText c0_",
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            \sprintf("SELECT NOT_SIMILAR_TO(e.text,'TEST') FROM %s e", ContainsText::class),
        ];
    }
}
