<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayTrim;
use PHPUnit\Framework\Attributes\Test;

class ArrayTrimTest extends ArrayTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ARRAY_TRIM' => ArrayTrim::class,
        ];
    }

    #[Test]
    public function can_trim_text_array_without_changes_when_zero(): void
    {
        $dql = 'SELECT ARRAY_TRIM(t.textArray, 0) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsArrays t 
                WHERE t.id = 1';

        $result = $this->executeDqlQuery($dql);
        $actual = $this->transformPostgresArray($result[0]['result']);
        $this->assertIsArray($actual);
        $this->assertSame(['apple', 'banana', 'orange'], $actual);
    }

    #[Test]
    public function can_trim_integer_array_without_changes_when_zero(): void
    {
        $dql = 'SELECT ARRAY_TRIM(t.integerArray, 0) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsArrays t 
                WHERE t.id = 1';

        $result = $this->executeDqlQuery($dql);
        $actual = $this->transformPostgresArray($result[0]['result']);
        $this->assertIsArray($actual);
        $this->assertSame([1, 2, 3], $actual);
    }
}
