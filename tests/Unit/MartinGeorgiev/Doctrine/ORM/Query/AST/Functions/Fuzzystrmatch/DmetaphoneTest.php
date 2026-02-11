<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Fuzzystrmatch;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsTexts;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Fuzzystrmatch\Dmetaphone;
use Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\TestCase;

class DmetaphoneTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'DMETAPHONE' => Dmetaphone::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'SELECT dmetaphone(c0_.text1) AS sclr_0 FROM ContainsTexts c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            \sprintf('SELECT DMETAPHONE(e.text1) FROM %s e', ContainsTexts::class),
        ];
    }
}
