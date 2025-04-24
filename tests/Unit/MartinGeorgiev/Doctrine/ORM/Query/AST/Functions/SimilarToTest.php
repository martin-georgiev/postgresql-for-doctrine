<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsTexts;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\SimilarTo;

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
            "SELECT c0_.text1 similar to 'TEST' AS sclr_0 FROM ContainsTexts c0_",
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            \sprintf("SELECT SIMILAR_TO(e.text1, 'TEST') FROM %s e", ContainsTexts::class),
        ];
    }
}
