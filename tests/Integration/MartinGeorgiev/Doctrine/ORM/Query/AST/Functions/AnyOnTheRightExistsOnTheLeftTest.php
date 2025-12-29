<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\AnyOnTheRightExistsOnTheLeft;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Arr;
use PHPUnit\Framework\Attributes\Test;

class AnyOnTheRightExistsOnTheLeftTest extends JsonTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ANY_ON_RIGHT_EXISTS_ON_LEFT' => AnyOnTheRightExistsOnTheLeft::class,
            'ARR' => Arr::class,
        ];
    }

    #[Test]
    public function returns_true_when_any_key_exists(): void
    {
        $dql = "SELECT ANY_ON_RIGHT_EXISTS_ON_LEFT(t.jsonbObject1, ARR('name', 'nonexistent')) as result 
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsJsons t 
                WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertTrue($result[0]['result']);
    }

    #[Test]
    public function returns_false_when_no_keys_exist(): void
    {
        $dql = "SELECT ANY_ON_RIGHT_EXISTS_ON_LEFT(t.jsonbObject1, ARR('nonexistent1', 'nonexistent2')) as result 
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsJsons t 
                WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertFalse($result[0]['result']);
    }
}
