<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Doctrine\ORM\Query\QueryException;
use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsJsons;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Json;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

final class JsonTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'JSON' => Json::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'parses a field' => 'SELECT json(c0_.jsonObject1) AS sclr_0 FROM ContainsJsons c0_',
            'parses a literal' => "SELECT json('{\"a\": 1}') AS sclr_0 FROM ContainsJsons c0_",
            'parses with unique keys' => 'SELECT json(c0_.jsonObject1 WITH UNIQUE KEYS) AS sclr_0 FROM ContainsJsons c0_',
            'parses with unique, keys omitted' => 'SELECT json(c0_.jsonObject1 WITH UNIQUE KEYS) AS sclr_0 FROM ContainsJsons c0_',
            'parses with lowercase keywords' => 'SELECT json(c0_.jsonObject1 WITH UNIQUE KEYS) AS sclr_0 FROM ContainsJsons c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'parses a field' => \sprintf('SELECT JSON(e.jsonObject1) FROM %s e', ContainsJsons::class),
            'parses a literal' => \sprintf("SELECT JSON('{\"a\": 1}') FROM %s e", ContainsJsons::class),
            'parses with unique keys' => \sprintf('SELECT JSON(e.jsonObject1 WITH UNIQUE KEYS) FROM %s e', ContainsJsons::class),
            'parses with unique, keys omitted' => \sprintf('SELECT JSON(e.jsonObject1 WITH UNIQUE) FROM %s e', ContainsJsons::class),
            'parses with lowercase keywords' => \sprintf('SELECT JSON(e.jsonObject1 with unique keys) FROM %s e', ContainsJsons::class),
        ];
    }

    #[DataProvider('provideMalformedUniqueKeysClauses')]
    #[Test]
    public function throws_exception_for_malformed_unique_keys_clause(string $dql): void
    {
        $this->expectException(QueryException::class);

        $this->buildEntityManager()->createQuery($dql)->getSQL();
    }

    /**
     * @return array<string, array{string}>
     */
    public static function provideMalformedUniqueKeysClauses(): array
    {
        return [
            'missing unique' => [\sprintf('SELECT JSON(e.jsonObject1 WITH KEYS) FROM %s e', ContainsJsons::class)],
            'nothing after with' => [\sprintf('SELECT JSON(e.jsonObject1 WITH) FROM %s e', ContainsJsons::class)],
            'unknown word after keys' => [\sprintf('SELECT JSON(e.jsonObject1 WITH UNIQUE KEYS ONLY) FROM %s e', ContainsJsons::class)],
        ];
    }
}
