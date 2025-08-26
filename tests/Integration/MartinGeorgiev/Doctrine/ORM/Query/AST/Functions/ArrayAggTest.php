<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayAgg;
use PHPUnit\Framework\Attributes\Test;

class ArrayAggTest extends ArrayTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ARRAY_AGG' => ArrayAgg::class,
        ];
    }

    #[Test]
    public function array_agg_with_text_array(): void
    {
        $dql = 'SELECT ARRAY_AGG(t.textArray) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsArrays t 
                WHERE t.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertSame('{{apple,banana,orange}}', $result[0]['result']);
    }

    #[Test]
    public function array_agg_with_integer_array(): void
    {
        $dql = 'SELECT ARRAY_AGG(t.integerArray) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsArrays t 
                WHERE t.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertSame('{{1,2,3}}', $result[0]['result']);
    }

    #[Test]
    public function array_agg_with_boolean_array(): void
    {
        $dql = 'SELECT ARRAY_AGG(t.boolArray) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsArrays t 
                WHERE t.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertSame('{{t,f,t}}', $result[0]['result']);
    }
}
