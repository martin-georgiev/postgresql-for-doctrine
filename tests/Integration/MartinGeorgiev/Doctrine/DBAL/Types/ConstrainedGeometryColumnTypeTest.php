<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\DBAL\Types;

final class ConstrainedGeometryColumnTypeTest extends ConstrainedSpatialColumnTypeTestCase
{
    protected function getTypeName(): string
    {
        return 'geometry';
    }
}
