<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsTexts;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Exception\InvalidArgumentForVariadicFunctionException;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\RegexpSplitToArray;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

final class RegexpSplitToArrayTest extends BaseVariadicFunctionTestCase
{
    protected function createFixture(): RegexpSplitToArray
    {
        return new RegexpSplitToArray('REGEXP_SPLIT_TO_ARRAY');
    }

    protected function getStringFunctions(): array
    {
        return [
            'REGEXP_SPLIT_TO_ARRAY' => RegexpSplitToArray::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'splits at a pattern' => "SELECT regexp_split_to_array(c0_.text1, ',') AS sclr_0 FROM ContainsTexts c0_",
            'splits at a pattern with flags' => "SELECT regexp_split_to_array(c0_.text1, 'and', 'i') AS sclr_0 FROM ContainsTexts c0_",
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'splits at a pattern' => \sprintf("SELECT REGEXP_SPLIT_TO_ARRAY(e.text1, ',') FROM %s e", ContainsTexts::class),
            'splits at a pattern with flags' => \sprintf("SELECT REGEXP_SPLIT_TO_ARRAY(e.text1, 'and', 'i') FROM %s e", ContainsTexts::class),
        ];
    }

    #[DataProvider('provideInvalidArgumentCounts')]
    #[Test]
    public function throws_exception_for_invalid_argument_count(string $dql): void
    {
        $this->expectException(InvalidArgumentForVariadicFunctionException::class);

        $this->buildEntityManager()->createQuery($dql)->getSQL();
    }

    /**
     * @return array<string, array{string}>
     */
    public static function provideInvalidArgumentCounts(): array
    {
        return [
            'too few' => [\sprintf('SELECT REGEXP_SPLIT_TO_ARRAY(e.text1) FROM %s e', ContainsTexts::class)],
            'too many' => [\sprintf("SELECT REGEXP_SPLIT_TO_ARRAY(e.text1, ',', 'i', 'extra') FROM %s e", ContainsTexts::class)],
        ];
    }
}
