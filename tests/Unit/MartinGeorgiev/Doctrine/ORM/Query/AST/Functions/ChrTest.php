<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsIntegers;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Chr;

class ChrTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'CHR' => Chr::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'returns character for a literal codepoint' => 'SELECT chr(65) AS sclr_0 FROM ContainsIntegers c0_',
            'returns character for an integer field' => 'SELECT chr(c0_.integer1) AS sclr_0 FROM ContainsIntegers c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'returns character for a literal codepoint' => \sprintf('SELECT CHR(65) FROM %s e', ContainsIntegers::class),
            'returns character for an integer field' => \sprintf('SELECT CHR(e.integer1) FROM %s e', ContainsIntegers::class),
        ];
    }
}
