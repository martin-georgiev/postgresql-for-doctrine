<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Row;
use PHPUnit\Framework\Attributes\Test;

class RowTest extends TextTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ROW' => Row::class,
        ];
    }

    #[Test]
    public function can_create_row_from_columns(): void
    {
        $dql = "SELECT t.id as result 
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t 
                WHERE ROW(t.text1, t.text2) = ROW('foo', 'bar')";
        $result = $this->executeDqlQuery($dql);
        $this->assertCount(1, $result);
        $this->assertSame(3, $result[0]['result']);
    }
}
