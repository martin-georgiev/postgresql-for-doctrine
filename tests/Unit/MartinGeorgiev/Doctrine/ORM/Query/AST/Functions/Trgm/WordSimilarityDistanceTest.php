<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Trgm;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsTexts;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Trgm\WordSimilarityDistance;
use Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\TestCase;

class WordSimilarityDistanceTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'WORD_SIMILARITY_DISTANCE' => WordSimilarityDistance::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'SELECT (c0_.text1 <<-> c0_.text2) AS sclr_0 FROM ContainsTexts c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            \sprintf('SELECT WORD_SIMILARITY_DISTANCE(e.text1, e.text2) FROM %s e', ContainsTexts::class),
        ];
    }
}
