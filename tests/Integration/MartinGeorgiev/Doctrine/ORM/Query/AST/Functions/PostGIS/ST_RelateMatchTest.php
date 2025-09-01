<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_RelateMatch;
use PHPUnit\Framework\Attributes\Test;

class ST_RelateMatchTest extends SpatialOperatorTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ST_RELATEMATCH' => ST_RelateMatch::class,
        ];
    }

    #[Test]
    public function returns_true_when_relate_pattern_matches(): void
    {
        $dql = 'SELECT ST_RelateMatch(\'T*T***T**\', \'T*T***T**\') as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertTrue($result[0]['result']);
    }

    #[Test]
    public function returns_false_when_relate_pattern_does_not_match(): void
    {
        $dql = 'SELECT ST_RelateMatch(\'FF*FF****\', \'T*T***T**\') as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertFalse($result[0]['result']);
    }

    #[Test]
    public function returns_true_when_relate_pattern_matches_in_where_clause(): void
    {
        $dql = 'SELECT ST_RelateMatch(\'T*T***T**\', \'T*T***T**\') as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE ST_RelateMatch(\'T*T***T**\', \'T*T***T**\') = TRUE';

        $result = $this->executeDqlQuery($dql);
        $this->assertTrue($result[0]['result']);
    }
}
