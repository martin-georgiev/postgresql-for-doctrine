<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Fuzzystrmatch;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsTexts;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Fuzzystrmatch\DmetaphoneAlt;
use Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\TestCase;

class DmetaphoneAltTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'DMETAPHONE_ALT' => DmetaphoneAlt::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'SELECT dmetaphone_alt(c0_.text1) AS sclr_0 FROM ContainsTexts c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            \sprintf('SELECT DMETAPHONE_ALT(e.text1) FROM %s e', ContainsTexts::class),
        ];
    }
}
