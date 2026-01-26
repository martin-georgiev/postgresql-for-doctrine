<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsDates;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\DatePart;

class DatePartTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'DATE_PART' => DatePart::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'extracts year part' => "SELECT date_part('year', c0_.datetime1) AS sclr_0 FROM ContainsDates c0_",
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'extracts year part' => \sprintf("SELECT DATE_PART('year', e.datetime1) FROM %s e", ContainsDates::class),
        ];
    }
}
