<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsTexts;
use Fixtures\MartinGeorgiev\Doctrine\Function\TestShortNodeMappingFunction;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Exception\InvalidArgumentForVariadicFunctionException;
use PHPUnit\Framework\Attributes\Test;

final class BaseVariadicFunctionShortNodeMappingTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'TEST_SHORT_NODE_MAPPING' => TestShortNodeMappingFunction::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'as many arguments as the node mapping pattern defines' => 'SELECT test_short_node_mapping(c0_.text1, c0_.text2) AS sclr_0 FROM ContainsTexts c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'as many arguments as the node mapping pattern defines' => \sprintf('SELECT TEST_SHORT_NODE_MAPPING(e.text1, e.text2) FROM %s e', ContainsTexts::class),
        ];
    }

    #[Test]
    public function throws_exception_when_the_node_mapping_pattern_is_shorter_than_the_provided_argument_count(): void
    {
        $this->expectException(InvalidArgumentForVariadicFunctionException::class);
        $this->expectExceptionMessage('test_short_node_mapping() cannot be called with 3 arguments, because implementation defines fewer node mappings than the actually provided argument count');

        $dql = \sprintf('SELECT TEST_SHORT_NODE_MAPPING(e.text1, e.text2, e.text1) FROM %s e', ContainsTexts::class);
        $this->buildEntityManager()->createQuery($dql)->getSQL();
    }
}
