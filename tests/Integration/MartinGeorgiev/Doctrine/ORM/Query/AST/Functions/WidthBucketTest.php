<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\WidthBucket;

class WidthBucketTest extends NumericTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'WIDTH_BUCKET' => WidthBucket::class,
        ];
    }

    public function test_width_bucket(): void
    {
        $dql = 'SELECT WIDTH_BUCKET(5.35, 0.024, 10.06, 5) as result FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics t WHERE t.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertSame(3, $result[0]['result']);
    }

    public function test_width_bucket_with_entity_property(): void
    {
        $dql = 'SELECT WIDTH_BUCKET(n.decimal1, 0.0, 20.0, 4) as result FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics n WHERE n.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertSame(3, $result[0]['result']);
    }
}
