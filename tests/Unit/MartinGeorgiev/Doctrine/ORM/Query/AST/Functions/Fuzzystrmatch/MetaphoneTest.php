<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Fuzzystrmatch;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsTexts;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Fuzzystrmatch\Metaphone;
use Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\TestCase;

class MetaphoneTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'METAPHONE' => Metaphone::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'SELECT metaphone(c0_.text1, 4) AS sclr_0 FROM ContainsTexts c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            \sprintf('SELECT METAPHONE(e.text1, 4) FROM %s e', ContainsTexts::class),
        ];
    }
}
