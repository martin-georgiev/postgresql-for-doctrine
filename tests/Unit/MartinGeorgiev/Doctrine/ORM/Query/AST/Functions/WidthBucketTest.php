<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsDecimals;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Exception\InvalidArgumentForVariadicFunctionException;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\WidthBucket;
use PHPUnit\Framework\Attributes\Test;

class WidthBucketTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'WIDTH_BUCKET' => WidthBucket::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'assigns field value to histogram bucket' => 'SELECT WIDTH_BUCKET(c0_.decimal1, 0.0, 20.0, 4) AS sclr_0 FROM ContainsDecimals c0_',
            'assigns literal value to histogram bucket' => 'SELECT WIDTH_BUCKET(15, 0.0, 20.0, 4) AS sclr_0 FROM ContainsDecimals c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'assigns field value to histogram bucket' => \sprintf('SELECT WIDTH_BUCKET(e.decimal1, 0.0, 20.0, 4) FROM %s e', ContainsDecimals::class),
            'assigns literal value to histogram bucket' => \sprintf('SELECT WIDTH_BUCKET(15, 0.0, 20.0, 4) FROM %s e', ContainsDecimals::class),
        ];
    }

    #[Test]
    public function throws_exception_for_too_few_arguments(): void
    {
        $this->expectException(InvalidArgumentForVariadicFunctionException::class);
        $this->expectExceptionMessage('WIDTH_BUCKET() requires exactly 4 arguments');

        $dql = \sprintf('SELECT WIDTH_BUCKET(e.decimal1, 0.0, 20.0) FROM %s e', ContainsDecimals::class);
        $this->buildEntityManager()->createQuery($dql)->getSQL();
    }

    #[Test]
    public function throws_exception_for_too_many_arguments(): void
    {
        $this->expectException(InvalidArgumentForVariadicFunctionException::class);
        $this->expectExceptionMessage('WIDTH_BUCKET() requires exactly 4 arguments');

        $dql = \sprintf('SELECT WIDTH_BUCKET(e.decimal1, 0.0, 20.0, 4, 5) FROM %s e', ContainsDecimals::class);
        $this->buildEntityManager()->createQuery($dql)->getSQL();
    }
}
