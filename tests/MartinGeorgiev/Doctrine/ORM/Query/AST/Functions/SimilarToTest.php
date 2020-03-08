<?php

declare(strict_types=1);

namespace MartinGeorgiev\Tests\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\SimilarTo;
use MartinGeorgiev\Tests\Doctrine\Fixtures\Entity\ContainsText;

class SimilarToTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'SIMILAR_TO' => SimilarTo::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            "SELECT c0_.text similar to 'TEST' AS sclr_0 FROM ContainsText c0_",
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            \sprintf("SELECT SIMILAR_TO(e.text,'TEST') FROM %s e", ContainsText::class),
        ];
    }
}
