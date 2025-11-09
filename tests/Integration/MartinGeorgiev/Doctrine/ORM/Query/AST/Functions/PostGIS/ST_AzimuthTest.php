<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Azimuth;
use PHPUnit\Framework\Attributes\Test;

class ST_AzimuthTest extends SpatialOperatorTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ST_AZIMUTH' => ST_Azimuth::class,
        ];
    }

    #[Test]
    public function returns_azimuth_between_two_known_points(): void
    {
        $dql = 'SELECT ST_AZIMUTH(g.geometry1, g.geometry2) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertEqualsWithDelta(0.7853981633974483, $result[0]['result'], 0.0000000000000001);
    }

    #[Test]
    public function returns_null_for_identical_points(): void
    {
        $dql = 'SELECT ST_AZIMUTH(g.geometry1, g.geometry1) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertNull($result[0]['result'], 'PostGIS behavior expects that azimuth is undefined for identical points');
    }

    #[Test]
    public function returns_valid_azimuth_range(): void
    {
        $dql = 'SELECT ST_AZIMUTH(g.geometry1, g.geometry2) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertGreaterThanOrEqual(0, $result[0]['result']);
        $this->assertLessThan(2 * M_PI, $result[0]['result']);
    }

    #[Test]
    public function returns_pi_for_south_direction(): void
    {
        $dql = 'SELECT ST_AZIMUTH(g.geometry2, g.geometry1) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertEqualsWithDelta(3.9269908169872423, $result[0]['result'], 0.0000000000000001);
    }
}
