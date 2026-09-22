<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\TheRightExistsOnTheLeft;
use PHPUnit\Framework\Attributes\Test;

final class TheRightExistsOnTheLeftTest extends JsonTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'RIGHT_EXISTS_ON_LEFT' => TheRightExistsOnTheLeft::class,
        ];
    }

    #[Test]
    public function returns_true_when_the_key_exists_in_an_entity_field(): void
    {
        $dql = "SELECT RIGHT_EXISTS_ON_LEFT(t.jsonbObject1, 'name') as result 
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsJsons t 
                WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertTrue($result[0]['result']);
    }

    #[Test]
    public function returns_false_when_the_key_is_absent_from_an_entity_field(): void
    {
        $dql = "SELECT RIGHT_EXISTS_ON_LEFT(t.jsonbObject1, 'nonexistent') as result 
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsJsons t 
                WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertFalse($result[0]['result']);
    }
}
