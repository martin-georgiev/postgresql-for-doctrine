<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsTexts;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BaseVariadicFunction;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Exception\InvalidArgumentForVariadicFunctionException;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonToTsvector;
use PHPUnit\Framework\Attributes\Test;

class JsonToTsvectorTest extends BaseVariadicFunctionTestCase
{
    protected function createFixture(): BaseVariadicFunction
    {
        return new JsonToTsvector('JSON_TO_TSVECTOR');
    }

    protected function getStringFunctions(): array
    {
        return [
            'JSON_TO_TSVECTOR' => JsonToTsvector::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'converts json to tsvector with filter' => "SELECT json_to_tsvector(c0_.text1, '[\"string\"]') AS sclr_0 FROM ContainsTexts c0_",
            'converts json to tsvector with config and filter' => "SELECT json_to_tsvector('english', c0_.text1, '[\"string\"]') AS sclr_0 FROM ContainsTexts c0_",
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'converts json to tsvector with filter' => \sprintf('SELECT JSON_TO_TSVECTOR(e.text1, \'["string"]\') FROM %s e', ContainsTexts::class),
            'converts json to tsvector with config and filter' => \sprintf('SELECT JSON_TO_TSVECTOR(\'english\', e.text1, \'["string"]\') FROM %s e', ContainsTexts::class),
        ];
    }

    #[Test]
    public function throws_exception_for_too_many_arguments(): void
    {
        $this->expectException(InvalidArgumentForVariadicFunctionException::class);

        $dql = \sprintf('SELECT JSON_TO_TSVECTOR(\'english\', e.text1, \'["string"]\', \'extra\') FROM %s e', ContainsTexts::class);
        $this->assertSqlFromDql('', $dql);
    }
}
