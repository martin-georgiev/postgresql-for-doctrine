<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\AnyOnTheRightExistsOnTheLeft;

class AnyOnTheRightExistsOnTheLeftTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return ['ANY_ON_THE_RIGHT_EXISTS_ON_THE_LEFT' => AnyOnTheRightExistsOnTheLeft::class];
    }

    public function test_any_on_the_right_exists_on_the_left_with_text_array(): void
    {
        $dql = "SELECT ANY_ON_THE_RIGHT_EXISTS_ON_THE_LEFT(t.textArray, ARRAY['apple', 'grape']) as result 
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsArrays t 
                WHERE t.id = 1";

        $result = $this->executeDqlQuery($dql);
        $this->assertTrue($result[0]['result']);
    }

    public function test_any_on_the_right_exists_on_the_left_with_integer_array(): void
    {
        $dql = 'SELECT ANY_ON_THE_RIGHT_EXISTS_ON_THE_LEFT(t.integerArray, ARRAY[1, 4]) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsArrays t 
                WHERE t.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertTrue($result[0]['result']);
    }

    public function test_any_on_the_right_exists_on_the_left_with_no_match(): void
    {
        $dql = "SELECT ANY_ON_THE_RIGHT_EXISTS_ON_THE_LEFT(t.textArray, ARRAY['mango', 'kiwi']) as result 
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsArrays t 
                WHERE t.id = 1";

        $result = $this->executeDqlQuery($dql);
        $this->assertFalse($result[0]['result']);
    }
}
