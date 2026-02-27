<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsTexts;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BaseVariadicFunction;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Exception\InvalidArgumentForVariadicFunctionException;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Setweight;
use PHPUnit\Framework\Attributes\Test;

class SetweightTest extends BaseVariadicFunctionTestCase
{
    protected function createFixture(): BaseVariadicFunction
    {
        return new Setweight('SETWEIGHT');
    }

    protected function getStringFunctions(): array
    {
        return [
            'SETWEIGHT' => Setweight::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'assigns weight to text field' => "SELECT setweight(c0_.text1, 'A') AS sclr_0 FROM ContainsTexts c0_",
            'assigns weight with lexemes filter' => "SELECT setweight(c0_.text1, 'B', '{lorem,ipsum}') AS sclr_0 FROM ContainsTexts c0_",
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'assigns weight to text field' => \sprintf("SELECT SETWEIGHT(e.text1, 'A') FROM %s e", ContainsTexts::class),
            'assigns weight with lexemes filter' => \sprintf("SELECT SETWEIGHT(e.text1, 'B', '{lorem,ipsum}') FROM %s e", ContainsTexts::class),
        ];
    }

    #[Test]
    public function throws_exception_for_too_many_arguments(): void
    {
        $this->expectException(InvalidArgumentForVariadicFunctionException::class);

        $dql = \sprintf("SELECT SETWEIGHT(e.text1, 'A', '{foo}', 'extra') FROM %s e", ContainsTexts::class);
        $this->assertSqlFromDql('', $dql);
    }
}
