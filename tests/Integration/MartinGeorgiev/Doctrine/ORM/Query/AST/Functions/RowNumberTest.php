<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Doctrine\DBAL\Exception\DriverException;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Over;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\RowNumber;
use PHPUnit\Framework\Attributes\Test;

final class RowNumberTest extends NumericTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'OVER' => Over::class,
            'ROW_NUMBER' => RowNumber::class,
        ];
    }

    #[Test]
    public function returns_the_row_number_from_a_numeric_literal(): void
    {
        $dql = 'SELECT OVER(ROW_NUMBER(), PARTITION BY 1 ORDER BY n.integer2) as result FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics n WHERE n.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertSame(1, $result[0]['result']);
    }

    #[Test]
    public function returns_the_row_number_from_entity_fields(): void
    {
        $dql = 'SELECT OVER(ROW_NUMBER(), PARTITION BY n.integer1 ORDER BY n.integer2) as result FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics n WHERE n.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertSame(1, $result[0]['result']);
    }

    #[Test]
    public function rejects_a_call_outside_over(): void
    {
        $this->expectException(DriverException::class);
        $dql = 'SELECT ROW_NUMBER() as result FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics n WHERE n.id = 1';
        $this->executeDqlQuery($dql);
    }
}
