<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types\ValueObject;

/**
 * These modifiers specify additional coordinate dimensions beyond the standard X and Y:
 * - Z: Elevation/altitude coordinate (3D)
 * - M: Measure coordinate (linear referencing)
 * - ZM: Both elevation and measure coordinates (4D)
 *
 * @since 3.5
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
enum DimensionalModifier: string
{
    /**
     * Z dimension - represents elevation/altitude coordinate.
     * Results in 3D geometries with X, Y, Z coordinates.
     */
    case Z = 'Z';

    /**
     * M dimension - represents measure coordinate for linear referencing.
     * Results in measured geometries with X, Y, M coordinates.
     */
    case M = 'M';

    /**
     * ZM dimensions - represents both elevation and measure coordinates.
     * Results in 4D geometries with X, Y, Z, M coordinates.
     */
    case ZM = 'ZM';
}
