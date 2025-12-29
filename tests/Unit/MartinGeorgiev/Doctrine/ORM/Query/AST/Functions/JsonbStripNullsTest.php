<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsJsons;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Exception\InvalidBooleanException;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonbStripNulls;
use PHPUnit\Framework\Attributes\Test;

class JsonbStripNullsTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'JSONB_STRIP_NULLS' => JsonbStripNulls::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'strips nulls with one parameter' => 'SELECT jsonb_strip_nulls(c0_.jsonbObject1) AS sclr_0 FROM ContainsJsons c0_',
            'strips nulls with null_value_treatment parameter' => "SELECT jsonb_strip_nulls(c0_.jsonbObject1, 'true') AS sclr_0 FROM ContainsJsons c0_",
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'strips nulls with one parameter' => \sprintf('SELECT JSONB_STRIP_NULLS(e.jsonbObject1) FROM %s e', ContainsJsons::class),
            'strips nulls with null_value_treatment parameter' => \sprintf("SELECT JSONB_STRIP_NULLS(e.jsonbObject1, 'true') FROM %s e", ContainsJsons::class),
        ];
    }

    #[Test]
    public function throws_exception_for_invalid_boolean(): void
    {
        $this->expectException(InvalidBooleanException::class);
        $this->expectExceptionMessage('Invalid boolean value "invalid" provided for jsonb_strip_nulls. Must be "true" or "false".');

        $dql = \sprintf("SELECT JSONB_STRIP_NULLS(e.jsonbObject1, 'invalid') FROM %s e", ContainsJsons::class);
        $this->buildEntityManager()->createQuery($dql)->getSQL();
    }

    #[Test]
    public function throws_exception_for_non_constant_boolean_parameter(): void
    {
        $this->expectException(InvalidBooleanException::class);
        $this->expectExceptionMessage('The boolean parameter for jsonb_strip_nulls must be a string literal');

        $dql = \sprintf('SELECT JSONB_STRIP_NULLS(e.jsonbObject1, e.jsonbObject1) FROM %s e', ContainsJsons::class);
        $this->buildEntityManager()->createQuery($dql)->getSQL();
    }
}
