<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Overlaps;
use PHPUnit\Framework\Attributes\Test;

class OverlapsTest extends ArrayTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'OVERLAPS' => Overlaps::class,
        ];
    }

    #[Test]
    public function returns_true_when_text_arrays_have_overlapping_elements(): void
    {
        $dql = 'SELECT OVERLAPS(t.textArray, :value) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsArrays t 
                WHERE t.id = 1';

        $result = $this->executeDqlQuery($dql, ['value' => ['apple', 'grape']]);
        $this->assertTrue($result[0]['result']);
    }

    #[Test]
    public function returns_true_when_integer_arrays_have_overlapping_elements(): void
    {
        $dql = 'SELECT OVERLAPS(t.integerArray, :value) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsArrays t 
                WHERE t.id = 1';

        $result = $this->executeDqlQuery($dql, ['value' => [2, 5, 6]]);
        $this->assertTrue($result[0]['result']);
    }

    #[Test]
    public function returns_false_when_arrays_have_no_overlapping_elements(): void
    {
        $dql = 'SELECT OVERLAPS(t.textArray, :value) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsArrays t 
                WHERE t.id = 1';

        $result = $this->executeDqlQuery($dql, ['value' => ['grape', 'kiwi']]);
        $this->assertFalse($result[0]['result']);
    }

    #[Test]
    public function returns_true_when_arrays_overlap_with_literal_array(): void
    {
        $dql = "SELECT OVERLAPS(t.textArray, '{\"banana\",\"grape\"}') as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsArrays t 
                WHERE t.id = 1";

        $result = $this->executeDqlQuery($dql);
        $this->assertTrue($result[0]['result']);
    }

    #[Test]
    public function returns_false_when_arrays_do_not_overlap_with_literal_array(): void
    {
        $dql = "SELECT OVERLAPS(t.textArray, '{\"grape\",\"kiwi\"}') as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsArrays t 
                WHERE t.id = 1";

        $result = $this->executeDqlQuery($dql);
        $this->assertFalse($result[0]['result']);
    }

    #[Test]
    public function returns_true_when_boolean_arrays_overlap(): void
    {
        $dql = 'SELECT OVERLAPS(t.boolArray, :value) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsArrays t 
                WHERE t.id = 1';

        $result = $this->executeDqlQuery($dql, ['value' => [true, false]]);
        $this->assertTrue($result[0]['result']);
    }

    #[Test]
    public function returns_true_when_comparing_two_database_arrays(): void
    {
        $dql = 'SELECT OVERLAPS(t1.textArray, t2.textArray) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsArrays t1,
                     Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsArrays t2
                WHERE t1.id = 1 AND t2.id = 2';

        $result = $this->executeDqlQuery($dql);
        $this->assertTrue($result[0]['result']); // Both contain 'apple'
    }

    #[Test]
    public function returns_true_when_comparing_overlapping_database_arrays(): void
    {
        $dql = 'SELECT OVERLAPS(t1.textArray, t2.textArray) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsArrays t1,
                     Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsArrays t2
                WHERE t1.id = 1 AND t2.id = 3';

        $result = $this->executeDqlQuery($dql);
        $this->assertTrue($result[0]['result']); // Both contain 'banana' and 'orange'
    }

    #[Test]
    public function returns_false_when_comparing_non_overlapping_database_arrays(): void
    {
        $dql = 'SELECT OVERLAPS(t1.textArray, t2.textArray) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsArrays t1,
                     Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsArrays t2
                WHERE t1.id = 2 AND t2.id = 3';

        $result = $this->executeDqlQuery($dql);
        $this->assertFalse($result[0]['result']); // Row 2: ['grape', 'apple'] vs Row 3: ['banana', 'orange', 'kiwi', 'mango'] - no overlap
    }
}
