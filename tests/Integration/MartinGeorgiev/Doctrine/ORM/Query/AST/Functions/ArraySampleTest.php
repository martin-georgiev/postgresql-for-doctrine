<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Arr;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArraySample;
use PHPUnit\Framework\Attributes\Test;

final class ArraySampleTest extends ArrayTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->requirePostgresVersion(160000, 'ARRAY_SAMPLE');
    }

    protected function getStringFunctions(): array
    {
        return [
            'ARR' => Arr::class,
            'ARRAY_SAMPLE' => ArraySample::class,
        ];
    }

    #[Test]
    public function returns_the_sampled_array_from_an_array_literal(): void
    {
        $dql = "SELECT ARRAY_SAMPLE(ARR('apple', 'banana', 'orange'), 2) as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsArrays t WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $actual = $this->transformPostgresArray($result[0]['result']);
        $this->assertIsArray($actual);
        $this->assertCount(2, $actual);
        foreach ($actual as $element) {
            $this->assertContains($element, ['apple', 'banana', 'orange']);
        }
    }

    #[Test]
    public function returns_the_sampled_array_from_an_entity_field(): void
    {
        $dql = 'SELECT ARRAY_SAMPLE(t.textArray, 3) as result FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsArrays t WHERE t.id = 1';
        $result = $this->executeDqlQuery($dql);
        $actual = $this->transformPostgresArray($result[0]['result']);
        $this->assertIsArray($actual);
        $this->assertCount(3, $actual);
        foreach ($actual as $element) {
            $this->assertContains($element, ['apple', 'banana', 'orange']);
        }
    }
}
