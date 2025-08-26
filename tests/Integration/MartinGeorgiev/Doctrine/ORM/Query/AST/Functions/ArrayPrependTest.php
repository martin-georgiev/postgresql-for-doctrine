<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayPrepend;
use PHPUnit\Framework\Attributes\Test;

class ArrayPrependTest extends ArrayTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ARRAY_PREPEND' => ArrayPrepend::class,
        ];
    }

    #[Test]
    public function can_prepend_to_text_array(): void
    {
        $dql = 'SELECT ARRAY_PREPEND(\'orange\', t.textArray) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsArrays t 
                WHERE t.id = 1';

        $result = $this->executeDqlQuery($dql);
        $actual = $this->transformPostgresArray($result[0]['result']);
        $this->assertIsArray($actual);
        $this->assertSame(['orange', 'apple', 'banana', 'orange'], $actual);
    }

    #[Test]
    public function can_prepend_to_integer_array(): void
    {
        $dql = 'SELECT ARRAY_PREPEND(3, t.integerArray) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsArrays t 
                WHERE t.id = 1';

        $result = $this->executeDqlQuery($dql);
        $actual = $this->transformPostgresArray($result[0]['result']);
        $this->assertIsArray($actual);
        $this->assertSame([3, 1, 2, 3], $actual);
    }

    #[Test]
    public function can_prepend_to_boolean_array(): void
    {
        $dql = 'SELECT ARRAY_PREPEND(true, t.boolArray) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsArrays t 
                WHERE t.id = 1';

        $result = $this->executeDqlQuery($dql);
        $actual = $this->transformPostgresArray($result[0]['result']);
        $this->assertIsArray($actual);
        $this->assertSame([true, true, false, true], $actual);
    }
}
