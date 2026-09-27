<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidSpatialColumnDeclarationException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

/**
 * The geometry_type and srid column options, shared by the scalar and the array spatial types.
 */
trait SpatialColumnOptionsTestTrait
{
    /**
     * @param array<string, mixed> $fieldDeclaration
     */
    abstract protected function getSQLDeclarationFor(array $fieldDeclaration): string;

    /**
     * The declaration expected for a type modifier such as '(POINT,4326)', or '' for none.
     */
    abstract protected function getExpectedSQLDeclaration(string $typeModifier): string;

    /**
     * @param array<string, mixed> $fieldDeclaration
     */
    #[DataProvider('provideColumnOptionsWithoutSpatialConstraints')]
    #[Test]
    public function returns_bare_type_for_column_options_without_spatial_constraints(array $fieldDeclaration): void
    {
        $this->assertSame($this->getExpectedSQLDeclaration(''), $this->getSQLDeclarationFor($fieldDeclaration));
    }

    /**
     * @return array<string, array{array<string, mixed>}>
     */
    public static function provideColumnOptionsWithoutSpatialConstraints(): array
    {
        return [
            'no options' => [[]],
            'unrelated options only' => [['notnull' => true, 'length' => 255]],
            'explicit nulls' => [['geometry_type' => null, 'srid' => null]],
        ];
    }

    /**
     * @param array<string, mixed> $fieldDeclaration
     */
    #[DataProvider('provideSpatialColumnOptions')]
    #[Test]
    public function returns_constrained_type_when_column_options_declared(array $fieldDeclaration, string $expectedTypeModifier): void
    {
        $this->assertSame($this->getExpectedSQLDeclaration($expectedTypeModifier), $this->getSQLDeclarationFor($fieldDeclaration));
    }

    /**
     * @return array<string, array{fieldDeclaration: array<string, mixed>, expectedTypeModifier: string}>
     */
    public static function provideSpatialColumnOptions(): array
    {
        return [
            'geometry type only' => [
                'fieldDeclaration' => ['geometry_type' => 'POINT'],
                'expectedTypeModifier' => '(POINT)',
            ],
            'geometry type and srid' => [
                'fieldDeclaration' => ['geometry_type' => 'POINT', 'srid' => 4326],
                'expectedTypeModifier' => '(POINT,4326)',
            ],
            'srid only falls back to the any-geometry subtype' => [
                'fieldDeclaration' => ['srid' => 4326],
                'expectedTypeModifier' => '(GEOMETRY,4326)',
            ],
            'srid zero' => [
                'fieldDeclaration' => ['geometry_type' => 'POINT', 'srid' => 0],
                'expectedTypeModifier' => '(POINT,0)',
            ],
            'srid as a digit string' => [
                'fieldDeclaration' => ['geometry_type' => 'POINT', 'srid' => '4326'],
                'expectedTypeModifier' => '(POINT,4326)',
            ],
            'lowercase geometry type' => [
                'fieldDeclaration' => ['geometry_type' => 'point', 'srid' => 4326],
                'expectedTypeModifier' => '(POINT,4326)',
            ],
            'padded geometry type' => [
                'fieldDeclaration' => ['geometry_type' => '  MultiPolygon  '],
                'expectedTypeModifier' => '(MULTIPOLYGON)',
            ],
            'geometry type with Z modifier' => [
                'fieldDeclaration' => ['geometry_type' => 'PointZ', 'srid' => 4326],
                'expectedTypeModifier' => '(POINTZ,4326)',
            ],
            'geometry type with M modifier' => [
                'fieldDeclaration' => ['geometry_type' => 'LineStringM'],
                'expectedTypeModifier' => '(LINESTRINGM)',
            ],
            'geometry type with ZM modifier' => [
                'fieldDeclaration' => ['geometry_type' => 'PolygonZM'],
                'expectedTypeModifier' => '(POLYGONZM)',
            ],
            'explicit any-geometry subtype' => [
                'fieldDeclaration' => ['geometry_type' => 'Geometry', 'srid' => 4326],
                'expectedTypeModifier' => '(GEOMETRY,4326)',
            ],
            'curved geometry type' => [
                'fieldDeclaration' => ['geometry_type' => 'CircularString', 'srid' => 4326],
                'expectedTypeModifier' => '(CIRCULARSTRING,4326)',
            ],
        ];
    }

    #[DataProvider('provideInvalidGeometryTypeOptions')]
    #[Test]
    public function throws_exception_for_invalid_geometry_type_option(mixed $geometryType): void
    {
        $this->expectException(InvalidSpatialColumnDeclarationException::class);

        $this->getSQLDeclarationFor(['geometry_type' => $geometryType]);
    }

    /**
     * @return array<string, array{mixed}>
     */
    public static function provideInvalidGeometryTypeOptions(): array
    {
        return [
            'unknown geometry type' => ['CUBE'],
            'unknown dimensional modifier' => ['PointX'],
            'empty string' => [''],
            'sql injection attempt' => ['POINT,4326); DROP TABLE users; --'],
            'integer' => [4326],
            'array' => [['POINT']],
            'boolean' => [true],
        ];
    }

    #[DataProvider('provideInvalidSridOptions')]
    #[Test]
    public function throws_exception_for_invalid_srid_option(mixed $srid): void
    {
        $this->expectException(InvalidSpatialColumnDeclarationException::class);

        $this->getSQLDeclarationFor(['geometry_type' => 'POINT', 'srid' => $srid]);
    }

    /**
     * @return array<string, array{mixed}>
     */
    public static function provideInvalidSridOptions(): array
    {
        return [
            'negative integer' => [-1],
            'non-numeric string' => ['four thousand'],
            'sql injection attempt' => ['4326); DROP TABLE users; --'],
            'float' => [4326.5],
            'array' => [[4326]],
            'boolean' => [true],
        ];
    }
}
