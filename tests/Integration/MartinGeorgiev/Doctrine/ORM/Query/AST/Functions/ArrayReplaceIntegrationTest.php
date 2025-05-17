<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayReplace;

class ArrayReplaceIntegrationTest extends IntegrationTestCase
{
    protected function getStringFunctions(): array
    {
        return ['ARRAY_REPLACE' => ArrayReplace::class];
    }

    public function test_array_replace_with_text_array(): void
    {
        $dql = "SELECT ARRAY_REPLACE(t.textArray, 'apple', 'pear') as replaced 
                FROM MartinGeorgiev\\Doctrine\\ORM\\Query\\AST\\Functions\\Entity\\ArrayTest t 
                WHERE t.id = 1";

        $result = $this->executeDqlQuery($dql);
        \assert(isset($result[0]['replaced']));

        $this->assertEquals(['pear', 'banana', 'pear'], $result[0]['replaced']);
    }

    public function test_array_replace_with_integer_array(): void
    {
        $dql = 'SELECT ARRAY_REPLACE(t.intArray, 1, 10) as replaced 
                FROM MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Entity\ArrayTest t 
                WHERE t.id = 1';

        $result = $this->executeDqlQuery($dql);
        \assert(isset($result[0]['replaced']));

        $this->assertEquals([10, 2, 10], $result[0]['replaced']);
    }

    public function test_array_replace_with_boolean_array(): void
    {
        $dql = 'SELECT ARRAY_REPLACE(t.boolArray, true, false) as replaced 
                FROM MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Entity\ArrayTest t 
                WHERE t.id = 1';

        $this->executeDqlQuery($dql);

        $updatedEntity = $this->entityManager->createQuery('SELECT t FROM MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Entity\ArrayTest t WHERE t.id = 1')->getOneOrNullResult();
        \assert($updatedEntity !== null && \is_object($updatedEntity) && \property_exists($updatedEntity, 'boolArray'));

        $this->assertEquals([false, false, false], $updatedEntity->boolArray);
    }
}
