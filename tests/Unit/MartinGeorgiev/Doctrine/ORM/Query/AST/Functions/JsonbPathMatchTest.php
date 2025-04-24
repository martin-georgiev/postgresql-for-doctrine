<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsJsons;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BaseVariadicFunction;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Exception\InvalidArgumentForVariadicFunctionException;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Exception\InvalidBooleanException;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonbPathMatch;

class JsonbPathMatchTest extends BaseVariadicFunctionTestCase
{
    protected function createFixture(): BaseVariadicFunction
    {
        return new JsonbPathMatch('JSONB_PATH_MATCH');
    }

    protected function getStringFunctions(): array
    {
        return [
            'JSONB_PATH_MATCH' => JsonbPathMatch::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'checks if path matches with exists condition' => "SELECT jsonb_path_match(c0_.object1, 'exists($.a[*] ? (@ >= 2 && @ <= 4))') AS sclr_0 FROM ContainsJsons c0_",
            'checks if path matches with simple condition' => "SELECT jsonb_path_match(c0_.object1, '$.a[*] > 2') AS sclr_0 FROM ContainsJsons c0_",
            'checks if path matches with simple condition and vars argument' => "SELECT jsonb_path_match(c0_.object1, '$.a[*] > 2', '{\"strict\": false}') AS sclr_0 FROM ContainsJsons c0_",
            'checks if path matches with simple condition and vars and silent arguments' => "SELECT jsonb_path_match(c0_.object1, '$.a[*] > 2', '{\"strict\": false}', 'true') AS sclr_0 FROM ContainsJsons c0_",
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'checks if path matches with exists condition' => \sprintf("SELECT JSONB_PATH_MATCH(e.object1, 'exists($.a[*] ? (@ >= 2 && @ <= 4))') FROM %s e", ContainsJsons::class),
            'checks if path matches with simple condition' => \sprintf("SELECT JSONB_PATH_MATCH(e.object1, '$.a[*] > 2') FROM %s e", ContainsJsons::class),
            'checks if path matches with simple condition and vars argument' => \sprintf("SELECT JSONB_PATH_MATCH(e.object1, '$.a[*] > 2', '{\"strict\": false}') FROM %s e", ContainsJsons::class),
            'checks if path matches with simple condition and vars and silent arguments' => \sprintf("SELECT JSONB_PATH_MATCH(e.object1, '$.a[*] > 2', '{\"strict\": false}', 'true') FROM %s e", ContainsJsons::class),
        ];
    }

    public function test_invalid_boolean_throws_exception(): void
    {
        $this->expectException(InvalidBooleanException::class);
        $this->expectExceptionMessage('Invalid boolean value "invalid" provided for jsonb_path_match. Must be "true" or "false".');

        $dql = \sprintf("SELECT JSONB_PATH_MATCH(e.object1, '$.items[*].id', '{\"strict\": false}', 'invalid') FROM %s e", ContainsJsons::class);
        $this->buildEntityManager()->createQuery($dql)->getSQL();
    }

    public function test_too_few_arguments_throws_exception(): void
    {
        $this->expectException(InvalidArgumentForVariadicFunctionException::class);
        $this->expectExceptionMessage('jsonb_path_match() requires at least 2 arguments');

        $dql = \sprintf('SELECT JSONB_PATH_MATCH(e.object1) FROM %s e', ContainsJsons::class);
        $this->buildEntityManager()->createQuery($dql)->getSQL();
    }

    public function test_too_many_arguments_throws_exception(): void
    {
        $this->expectException(InvalidArgumentForVariadicFunctionException::class);
        $this->expectExceptionMessage('jsonb_path_match() requires between 2 and 4 arguments');

        $dql = \sprintf("SELECT JSONB_PATH_MATCH(e.object1, '$.items[*].id', '{\"strict\": false}', 'true', 'extra_arg') FROM %s e", ContainsJsons::class);
        $this->buildEntityManager()->createQuery($dql)->getSQL();
    }
}
