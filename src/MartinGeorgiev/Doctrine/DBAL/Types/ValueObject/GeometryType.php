<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types\ValueObject;

/**
 * This enum represents geometry types used across various contexts including:
 * - WKT/EWKT parsing and generation
 * - PostgreSQL format normalization
 *
 * @since 3.5
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
enum GeometryType: string
{
    // Basic geometry types
    case POINT = 'POINT';
    case LINESTRING = 'LINESTRING';
    case POLYGON = 'POLYGON';

    // Multi-geometry types
    case MULTIPOINT = 'MULTIPOINT';
    case MULTILINESTRING = 'MULTILINESTRING';
    case MULTIPOLYGON = 'MULTIPOLYGON';

    // Collection types
    case GEOMETRYCOLLECTION = 'GEOMETRYCOLLECTION';

    // Circular geometry types (PostGIS extensions)
    case CIRCULARSTRING = 'CIRCULARSTRING';
    case COMPOUNDCURVE = 'COMPOUNDCURVE';
    case CURVEPOLYGON = 'CURVEPOLYGON';
    case MULTICURVE = 'MULTICURVE';
    case MULTISURFACE = 'MULTISURFACE';

    // Triangle and TIN types
    case TRIANGLE = 'TRIANGLE';
    case TIN = 'TIN';
    case POLYHEDRALSURFACE = 'POLYHEDRALSURFACE';
}
