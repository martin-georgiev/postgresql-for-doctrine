<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Doctrine\ORM\Tools\Pagination\Paginator;
use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Exception\ParserException;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Over;
use PHPUnit\Framework\Attributes\Test;

final class OverTest extends NumericTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'OVER' => Over::class,
        ];
    }

    #[Test]
    public function returns_the_windowed_aggregate_from_a_numeric_literal(): void
    {
        $dql = 'SELECT OVER(SUM(n.integer1), PARTITION BY 1) as result FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics n WHERE n.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertSame(10, $result[0]['result']);
    }

    #[Test]
    public function returns_the_windowed_aggregate_from_entity_fields(): void
    {
        $dql = 'SELECT OVER(SUM(n.integer1), PARTITION BY n.integer1 ORDER BY n.integer2) as result FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics n WHERE n.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertSame(10, $result[0]['result']);
    }

    #[Test]
    public function returns_the_windowed_aggregate_with_a_frame(): void
    {
        $dql = 'SELECT OVER(SUM(n.integer1), PARTITION BY n.integer1 ORDER BY n.integer2 ROWS BETWEEN UNBOUNDED PRECEDING AND CURRENT ROW) as result FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics n WHERE n.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertSame(10, $result[0]['result']);
    }

    #[Test]
    public function returns_the_windowed_aggregate_through_the_paginator(): void
    {
        $dql = 'SELECT n, OVER(COUNT(n.id), PARTITION BY n.integer1 ORDER BY n.integer2) as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsNumerics n WHERE n.id = 1 ORDER BY n.integer1';
        $query = $this->entityManager->createQuery($dql)->setFirstResult(0)->setMaxResults(10);
        $paginator = new Paginator($query);

        $this->assertCount(1, $paginator);
        $rows = \iterator_to_array($paginator);
        $this->assertIsArray($rows[0]);
        $this->assertInstanceOf(ContainsNumerics::class, $rows[0][0]);
        $this->assertSame(1, $rows[0]['result']);
    }

    #[Test]
    public function rejects_a_scalar_function(): void
    {
        $this->expectException(ParserException::class);
        $dql = 'SELECT OVER(ABS(n.integer1), PARTITION BY n.integer1) as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsNumerics n WHERE n.id = 1';
        $this->executeDqlQuery($dql);
    }

    #[Test]
    public function rejects_a_nested_over(): void
    {
        $this->expectException(ParserException::class);
        $dql = 'SELECT OVER(OVER(COUNT(n.id)), PARTITION BY n.integer1) as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsNumerics n WHERE n.id = 1';
        $this->executeDqlQuery($dql);
    }
}
