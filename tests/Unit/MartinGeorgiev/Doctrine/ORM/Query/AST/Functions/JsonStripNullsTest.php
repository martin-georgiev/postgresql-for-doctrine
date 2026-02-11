<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsJsons;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Exception\InvalidBooleanException;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonStripNulls;
use PHPUnit\Framework\Attributes\Test;

class JsonStripNullsTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'JSON_STRIP_NULLS' => JsonStripNulls::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'strips nulls with one parameter' => 'SELECT json_strip_nulls(c0_.jsonObject1) AS sclr_0 FROM ContainsJsons c0_',
            'strips nulls with null_value_treatment parameter' => "SELECT json_strip_nulls(c0_.jsonObject1, 'true') AS sclr_0 FROM ContainsJsons c0_",
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'strips nulls with one parameter' => \sprintf('SELECT JSON_STRIP_NULLS(e.jsonObject1) FROM %s e', ContainsJsons::class),
            'strips nulls with null_value_treatment parameter' => \sprintf("SELECT JSON_STRIP_NULLS(e.jsonObject1, 'true') FROM %s e", ContainsJsons::class),
        ];
    }

    #[Test]
    public function throws_exception_for_invalid_boolean(): void
    {
        $this->expectException(InvalidBooleanException::class);
        $this->expectExceptionMessage('Invalid boolean value "invalid" provided for json_strip_nulls. Must be "true" or "false".');

        $dql = \sprintf("SELECT JSON_STRIP_NULLS(e.jsonObject1, 'invalid') FROM %s e", ContainsJsons::class);
        $this->buildEntityManager()->createQuery($dql)->getSQL();
    }

    #[Test]
    public function throws_exception_for_non_constant_boolean_parameter(): void
    {
        $this->expectException(InvalidBooleanException::class);
        $this->expectExceptionMessage('The boolean parameter for json_strip_nulls must be a string literal');

        $dql = \sprintf('SELECT JSON_STRIP_NULLS(e.jsonObject1, e.jsonObject1) FROM %s e', ContainsJsons::class);
        $this->buildEntityManager()->createQuery($dql)->getSQL();
    }
}
