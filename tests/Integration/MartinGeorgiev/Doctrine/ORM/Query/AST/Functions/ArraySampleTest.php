<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArraySample;
use PHPUnit\Framework\Attributes\Test;

class ArraySampleTest extends ArrayTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->requirePostgresVersion(160000, 'ARRAY_SAMPLE');
    }

    protected function getStringFunctions(): array
    {
        return [
            'ARRAY_SAMPLE' => ArraySample::class,
        ];
    }

    #[Test]
    public function can_sample_elements_from_array_field(): void
    {
        $dql = 'SELECT a.textArray as source, ARRAY_SAMPLE(a.textArray, 3) as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsArrays a
                WHERE a.id = 1';

        $result = $this->executeDqlQuery($dql);

        $this->assertIsArray($result[0]['source']);

        $this->assertIsString($result[0]['result']);
        $sampledArray = $this->transformPostgresArray($result[0]['result']);
        $this->assertIsArray($sampledArray);

        $this->assertCount(3, $sampledArray);
        foreach ($sampledArray as $element) {
            $this->assertContains($element, $result[0]['source']);
        }
    }

    #[Test]
    public function can_sample_single_element(): void
    {
        $dql = 'SELECT a.textArray as source, ARRAY_SAMPLE(a.textArray, 1) as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsArrays a
                WHERE a.id = 3';

        $result = $this->executeDqlQuery($dql);

        $this->assertIsArray($result[0]['source']);

        $this->assertIsString($result[0]['result']);
        $sampledArray = $this->transformPostgresArray($result[0]['result']);

        $this->assertIsArray($sampledArray);

        $this->assertCount(1, $sampledArray);
        $this->assertContains($sampledArray[0], $result[0]['source']);
    }
}
