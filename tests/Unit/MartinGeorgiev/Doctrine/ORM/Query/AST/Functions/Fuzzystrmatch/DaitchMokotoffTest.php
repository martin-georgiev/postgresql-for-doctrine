<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Fuzzystrmatch;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsTexts;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Fuzzystrmatch\DaitchMokotoff;
use Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\TestCase;

class DaitchMokotoffTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'DAITCH_MOKOTOFF' => DaitchMokotoff::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'SELECT daitch_mokotoff(c0_.text1) AS sclr_0 FROM ContainsTexts c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            \sprintf('SELECT DAITCH_MOKOTOFF(e.text1) FROM %s e', ContainsTexts::class),
        ];
    }
}
