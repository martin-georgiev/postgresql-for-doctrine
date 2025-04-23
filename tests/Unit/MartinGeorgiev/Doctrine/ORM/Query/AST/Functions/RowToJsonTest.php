<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsTexts;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BaseVariadicFunction;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Exception\InvalidArgumentForVariadicFunctionException;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Exception\InvalidBooleanException;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\RowToJson;

class RowToJsonTest extends BaseVariadicFunctionTestCase
{
    protected function createFixture(): BaseVariadicFunction
    {
        return new RowToJson('ROW_TO_JSON');
    }

    protected function getStringFunctions(): array
    {
        return [
            'ROW_TO_JSON' => RowToJson::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'converts row to json' => 'SELECT row_to_json(c0_.text1) AS sclr_0 FROM ContainsTexts c0_',
            'converts row with expression to json' => 'SELECT row_to_json(UPPER(c0_.text1)) AS sclr_0 FROM ContainsTexts c0_',
            'converts row to json with pretty print' => "SELECT row_to_json(c0_.text1, 'true') AS sclr_0 FROM ContainsTexts c0_",
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'converts row to json' => \sprintf('SELECT ROW_TO_JSON(e.text1) FROM %s e', ContainsTexts::class),
            'converts row with expression to json' => \sprintf('SELECT ROW_TO_JSON(UPPER(e.text1)) FROM %s e', ContainsTexts::class),
            'converts row to json with pretty print' => \sprintf("SELECT ROW_TO_JSON(e.text1, 'true') FROM %s e", ContainsTexts::class),
        ];
    }

    public function test_invalid_boolean_throws_exception(): void
    {
        $this->expectException(InvalidBooleanException::class);
        $this->expectExceptionMessage('Invalid boolean value "invalid" provided for row_to_json. Must be "true" or "false".');

        $dql = \sprintf("SELECT ROW_TO_JSON(e.text1, 'invalid') FROM %s e", ContainsTexts::class);
        $this->buildEntityManager()->createQuery($dql)->getSQL();
    }

    public function test_too_many_arguments_throws_exception(): void
    {
        $this->expectException(InvalidArgumentForVariadicFunctionException::class);
        $this->expectExceptionMessage('row_to_json() requires between 1 and 2 arguments');

        $dql = \sprintf("SELECT ROW_TO_JSON(e.text1, 'true', 'extra_arg') FROM %s e", ContainsTexts::class);
        $this->buildEntityManager()->createQuery($dql)->getSQL();
    }
}
