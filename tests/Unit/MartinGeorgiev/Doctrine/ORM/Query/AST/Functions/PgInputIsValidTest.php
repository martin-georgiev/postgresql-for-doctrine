<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsTexts;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PgInputIsValid;

final class PgInputIsValidTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'PG_INPUT_IS_VALID' => PgInputIsValid::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'checks if text is valid input for a type' => "SELECT pg_input_is_valid(c0_.text1, 'integer') AS sclr_0 FROM ContainsTexts c0_",
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'checks if text is valid input for a type' => \sprintf("SELECT PG_INPUT_IS_VALID(e.text1, 'integer') FROM %s e", ContainsTexts::class),
        ];
    }
}
