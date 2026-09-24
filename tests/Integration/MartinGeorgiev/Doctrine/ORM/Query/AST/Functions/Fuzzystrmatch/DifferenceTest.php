<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Fuzzystrmatch;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Fuzzystrmatch\Difference;
use PHPUnit\Framework\Attributes\Test;

final class DifferenceTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'DIFFERENCE' => Difference::class,
        ];
    }

    #[Test]
    public function returns_the_similarity_score_from_text_literals(): void
    {
        $dql = "SELECT DIFFERENCE('Anne', 'Anton') as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t
                WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertSame(2, $result[0]['result']);
    }

    #[Test]
    public function returns_the_similarity_score_from_entity_fields(): void
    {
        $dql = 'SELECT DIFFERENCE(t.text1, t.text2) as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t
                WHERE t.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertSame(0, $result[0]['result']);
    }
}
