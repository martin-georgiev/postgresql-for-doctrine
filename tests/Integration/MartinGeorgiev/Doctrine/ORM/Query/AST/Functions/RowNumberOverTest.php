<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Doctrine\ORM\Tools\Pagination\Paginator;
use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\RowNumberOver;
use PHPUnit\Framework\Attributes\Test;

final class RowNumberOverTest extends NumericTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ROW_NUMBER_OVER' => RowNumberOver::class,
        ];
    }

    #[Test]
    public function returns_the_row_number_from_a_numeric_literal(): void
    {
        $dql = 'SELECT ROW_NUMBER_OVER(PARTITION BY 1 ORDER BY n.integer2) as result FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics n WHERE n.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertSame(1, $result[0]['result']);
    }

    #[Test]
    public function returns_the_row_number_from_entity_fields(): void
    {
        $dql = 'SELECT ROW_NUMBER_OVER(PARTITION BY n.integer1 ORDER BY n.integer2) as result FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics n WHERE n.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertSame(1, $result[0]['result']);
    }

    #[Test]
    public function returns_the_row_number_with_a_frame(): void
    {
        $dql = 'SELECT ROW_NUMBER_OVER(PARTITION BY n.integer1 ORDER BY n.integer2 ROWS BETWEEN UNBOUNDED PRECEDING AND CURRENT ROW EXCLUDE NO OTHERS) as result FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics n WHERE n.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertSame(1, $result[0]['result']);
    }

    #[Test]
    public function returns_the_row_number_through_the_paginator(): void
    {
        $dql = 'SELECT n, ROW_NUMBER_OVER(PARTITION BY n.integer1 ORDER BY n.integer2) as result FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics n WHERE n.id = 1 ORDER BY n.integer1';
        $query = $this->entityManager->createQuery($dql)->setFirstResult(0)->setMaxResults(10);
        $paginator = new Paginator($query);

        $this->assertCount(1, $paginator);
        $rows = \iterator_to_array($paginator);
        $this->assertIsArray($rows[0]);
        $this->assertInstanceOf(ContainsNumerics::class, $rows[0][0]);
        $this->assertSame(1, $rows[0]['result']);
    }
}
