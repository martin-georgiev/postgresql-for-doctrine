<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsTexts;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\NotSimilarTo;

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
            'SQL regex pattern non-matching' => "SELECT c0_.text1 not similar to 'TEST' AS sclr_0 FROM ContainsTexts c0_",
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'SQL regex pattern non-matching' => \sprintf("SELECT NOT_SIMILAR_TO(e.text1,'TEST') FROM %s e", ContainsTexts::class),
        ];
    }
}
