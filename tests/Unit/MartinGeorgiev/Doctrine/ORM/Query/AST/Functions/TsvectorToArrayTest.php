<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsTexts;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ToTsvector;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\TsvectorToArray;

class TsvectorToArrayTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'TO_TSVECTOR' => ToTsvector::class,
            'TSVECTOR_TO_ARRAY' => TsvectorToArray::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'converts tsvector field to array' => 'SELECT tsvector_to_array(c0_.text1) AS sclr_0 FROM ContainsTexts c0_',
            'converts tsvector function result to array' => 'SELECT tsvector_to_array(to_tsvector(c0_.text1)) AS sclr_0 FROM ContainsTexts c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'converts tsvector field to array' => \sprintf('SELECT TSVECTOR_TO_ARRAY(e.text1) FROM %s e', ContainsTexts::class),
            'converts tsvector function result to array' => \sprintf('SELECT TSVECTOR_TO_ARRAY(TO_TSVECTOR(e.text1)) FROM %s e', ContainsTexts::class),
        ];
    }
}
