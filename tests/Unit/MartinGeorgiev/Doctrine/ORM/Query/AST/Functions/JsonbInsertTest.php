<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsJsons;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Exception\InvalidArgumentForVariadicFunctionException;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Exception\InvalidBooleanException;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonbInsert;

class JsonbInsertTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'JSONB_INSERT' => JsonbInsert::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'basic usage' => "SELECT jsonb_insert(c0_.object1, '{country}', '{\"iso_3166_a3_code\":\"BGR\"}') AS sclr_0 FROM ContainsJsons c0_",
            'with create-if-missing parameter' => "SELECT jsonb_insert(c0_.object1, '{country}', '{\"iso_3166_a3_code\":\"BGR\"}', 'true') AS sclr_0 FROM ContainsJsons c0_",
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'basic usage' => \sprintf("SELECT JSONB_INSERT(e.object1, '{country}', '{\"iso_3166_a3_code\":\"BGR\"}') FROM %s e", ContainsJsons::class),
            'with create-if-missing parameter' => \sprintf("SELECT JSONB_INSERT(e.object1, '{country}', '{\"iso_3166_a3_code\":\"BGR\"}', 'true') FROM %s e", ContainsJsons::class),
        ];
    }

    public function test_invalid_boolean_throws_exception(): void
    {
        $this->expectException(InvalidBooleanException::class);
        $this->expectExceptionMessage('Invalid boolean value "invalid" provided for jsonb_insert. Must be "true" or "false".');

        $dql = \sprintf("SELECT JSONB_INSERT(e.object1, '{country}', '{\"iso_3166_a3_code\":\"bgr\"}', 'invalid') FROM %s e", ContainsJsons::class);
        $this->buildEntityManager()->createQuery($dql)->getSQL();
    }

    public function test_too_few_arguments_throws_exception(): void
    {
        $this->expectException(InvalidArgumentForVariadicFunctionException::class);
        $this->expectExceptionMessage('jsonb_insert() requires at least 3 arguments');

        $dql = \sprintf('SELECT JSONB_INSERT(e.object1) FROM %s e', ContainsJsons::class);
        $this->buildEntityManager()->createQuery($dql)->getSQL();
    }

    public function test_too_many_arguments_throws_exception(): void
    {
        $this->expectException(InvalidArgumentForVariadicFunctionException::class);
        $this->expectExceptionMessage('jsonb_insert() requires between 3 and 4 arguments');

        $dql = \sprintf("SELECT JSONB_INSERT(e.object1, '{country}', '{\"iso_3166_a3_code\":\"bgr\"}', 'true', 'extra_arg') FROM %s e", ContainsJsons::class);
        $this->buildEntityManager()->createQuery($dql)->getSQL();
    }
}
