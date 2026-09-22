<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Overlaps;
use PHPUnit\Framework\Attributes\Test;

final class OverlapsTest extends ArrayTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'OVERLAPS' => Overlaps::class,
        ];
    }

    #[Test]
    public function returns_true_when_an_entity_field_overlaps_a_literal(): void
    {
        $dql = "SELECT OVERLAPS(t.textArray, '{\"banana\",\"grape\"}') as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsArrays t 
                WHERE t.id = 1";

        $result = $this->executeDqlQuery($dql);
        $this->assertTrue($result[0]['result']);
    }

    #[Test]
    public function returns_true_when_two_entity_fields_overlap(): void
    {
        $dql = 'SELECT OVERLAPS(t1.textArray, t2.textArray) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsArrays t1,
                     Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsArrays t2
                WHERE t1.id = 1 AND t2.id = 2';

        $result = $this->executeDqlQuery($dql);
        $this->assertTrue($result[0]['result']); // Both contain 'apple'
    }
}
