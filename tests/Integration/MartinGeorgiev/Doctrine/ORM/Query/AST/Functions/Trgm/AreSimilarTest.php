<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Trgm;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Trgm\AreSimilar;
use PHPUnit\Framework\Attributes\Test;

final class AreSimilarTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ARE_SIMILAR' => AreSimilar::class,
        ];
    }

    #[Test]
    public function returns_whether_the_operands_are_similar_from_entity_fields(): void
    {
        $dql = 'SELECT t.id FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsTexts t
                WHERE ARE_SIMILAR(t.text1, t.text2) = TRUE AND t.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertCount(1, $result);
    }
}
