<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\DBAL\Types;

final class ConstrainedGeographyColumnTypeTest extends ConstrainedSpatialColumnTypeTestCase
{
    protected function getTypeName(): string
    {
        return 'geography';
    }
}
