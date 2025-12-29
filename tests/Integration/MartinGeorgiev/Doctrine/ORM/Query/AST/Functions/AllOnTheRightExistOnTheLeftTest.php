<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\AllOnTheRightExistOnTheLeft;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Arr;
use PHPUnit\Framework\Attributes\Test;

class AllOnTheRightExistOnTheLeftTest extends JsonTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ALL_ON_RIGHT_EXIST_ON_LEFT' => AllOnTheRightExistOnTheLeft::class,
            'ARR' => Arr::class,
        ];
    }

    #[Test]
    public function returns_true_when_all_keys_exist(): void
    {
        $dql = "SELECT ALL_ON_RIGHT_EXIST_ON_LEFT(t.jsonbObject1, ARR('name', 'age')) as result 
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsJsons t 
                WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertTrue($result[0]['result']);
    }

    #[Test]
    public function returns_false_when_not_all_keys_exist(): void
    {
        $dql = "SELECT ALL_ON_RIGHT_EXIST_ON_LEFT(t.jsonbObject1, ARR('name', 'nonexistent')) as result 
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsJsons t 
                WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertFalse($result[0]['result']);
    }
}
