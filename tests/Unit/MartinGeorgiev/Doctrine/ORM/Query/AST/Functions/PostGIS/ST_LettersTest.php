<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Letters;
use Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\TestCase;

class ST_LettersTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ST_LETTERS' => ST_Letters::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'with one argument' => "SELECT ST_Letters('ABC') AS sclr_0 FROM ContainsGeometries c0_",
            'with two arguments (custom font)' => "SELECT ST_Letters('ABC', '{\"A\": \"...\"}') AS sclr_0 FROM ContainsGeometries c0_",
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'with one argument' => \sprintf("SELECT ST_LETTERS('ABC') FROM %s g", ContainsGeometries::class),
            'with two arguments (custom font)' => \sprintf("SELECT ST_LETTERS('ABC', '{\"A\": \"...\"}') FROM %s g", ContainsGeometries::class),
        ];
    }
}
