<?php

declare(strict_types=1);

namespace Tests\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\NotSimilarTo;
use Tests\MartinGeorgiev\Doctrine\Fixtures\Entity\ContainsTexts;

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
            "SELECT c0_.text1 not similar to 'TEST' AS sclr_0 FROM ContainsTexts c0_",
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            \sprintf("SELECT NOT_SIMILAR_TO(e.text1,'TEST') FROM %s e", ContainsTexts::class),
        ];
    }
}
