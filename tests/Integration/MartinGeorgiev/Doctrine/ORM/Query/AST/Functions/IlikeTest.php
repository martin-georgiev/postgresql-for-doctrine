<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Ilike;
use PHPUnit\Framework\Attributes\Test;

final class IlikeTest extends TextTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ILIKE' => Ilike::class,
        ];
    }

    #[Test]
    public function returns_true_when_an_entity_field_matches_the_pattern(): void
    {
        $dql = "SELECT ILIKE(t.text1, '%test%') as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertTrue($result[0]['result']);
    }

    #[Test]
    public function returns_true_when_a_text_literal_matches_the_pattern(): void
    {
        $dql = "SELECT ILIKE('this is a test string', '%TEST%') as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertTrue($result[0]['result']);
    }
}
