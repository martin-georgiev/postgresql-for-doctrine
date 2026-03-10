<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Trgm;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Trgm\AreSimilar;
use PHPUnit\Framework\Attributes\Test;

class AreSimilarTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ARE_SIMILAR' => AreSimilar::class,
        ];
    }

    #[Test]
    public function returns_true_for_identical_strings(): void
    {
        $dql = "SELECT ARE_SIMILAR('word', 'word') as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t
                WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertEquals('t', $result[0]['result']);
    }

    #[Test]
    public function returns_false_for_completely_different_strings(): void
    {
        $dql = "SELECT ARE_SIMILAR('word', 'xyz') as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t
                WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertEquals('f', $result[0]['result']);
    }
}
