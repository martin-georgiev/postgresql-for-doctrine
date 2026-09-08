<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Types\Type;
use Fixtures\MartinGeorgiev\Doctrine\ConcreteInventoryItemType;
use Fixtures\MartinGeorgiev\Doctrine\ConcreteShipmentType;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

final class CompositeNestedTypeTest extends TestCase
{
    private const ITEM_TYPE_NAME = 'test_inventory_item';

    private const DBAL_TYPE_NAME = 'test_shipment';

    protected function setUp(): void
    {
        parent::setUp();

        $this->connection->executeStatement(\sprintf(
            'CREATE TYPE %s.%s AS (name text, supplier_id integer, price numeric)',
            self::DATABASE_SCHEMA,
            self::ITEM_TYPE_NAME
        ));
        $this->connection->executeStatement(\sprintf(
            'CREATE TYPE %s.%s AS (label text, item %s.%s, packed_at timestamp, delivered_at timestamptz, dispatched_on date, is_express boolean, tracking_id uuid, weight double precision, metadata json)',
            self::DATABASE_SCHEMA,
            self::DBAL_TYPE_NAME,
            self::DATABASE_SCHEMA,
            self::ITEM_TYPE_NAME
        ));

        $this->registerCompositeType(self::ITEM_TYPE_NAME, ConcreteInventoryItemType::class);
        $this->registerCompositeType(self::DBAL_TYPE_NAME, ConcreteShipmentType::class);
    }

    /**
     * @param class-string<Type> $typeClass
     */
    private function registerCompositeType(string $typeName, string $typeClass): void
    {
        if (Type::hasType($typeName)) {
            Type::overrideType($typeName, $typeClass);
        } else {
            Type::addType($typeName, $typeClass);
        }

        $this->connection->getDatabasePlatform()->registerDoctrineTypeMapping($typeName, $typeName);
    }

    protected function getTypeName(): string
    {
        return self::DBAL_TYPE_NAME;
    }

    #[Test]
    public function type_will_be_registered(): void
    {
        $typeName = $this->getTypeName();
        $this->assertTrue(Type::hasType($typeName));

        $type = Type::getType($typeName);
        $platform = $this->connection->getDatabasePlatform();

        $this->assertSame($typeName, $type->getSQLDeclaration([], $platform));

        if (\method_exists($type, 'requiresSQLCommentHint')) {
            $this->assertFalse($type->requiresSQLCommentHint($platform)); // @phpstan-ignore-line
        }
    }

    /**
     * @param array<string, mixed> $phpValue
     */
    #[DataProvider('provideValidWideFieldTransformations')]
    #[Test]
    public function roundtrips_value_with_non_string_field_types(array $phpValue): void
    {
        $typeName = $this->getTypeName();
        $columnType = Type::getType($typeName)->getSQLDeclaration([], $this->connection->getDatabasePlatform());
        $this->runDbalBindingRoundTrip($typeName, $columnType, $phpValue);
    }

    /**
     * @return array<string, array{array<string, mixed>}>
     */
    public static function provideValidWideFieldTransformations(): array
    {
        $item = ['name' => 'widget', 'supplier_id' => 42, 'price' => '9.99'];

        return [
            'populated' => [[
                'label' => 'bulk',
                'item' => $item,
                'packed_at' => new \DateTimeImmutable('2024-01-15 10:30:00'),
                'delivered_at' => new \DateTimeImmutable('2024-01-15 10:30:00', new \DateTimeZone('UTC')),
                'dispatched_on' => new \DateTimeImmutable('2024-01-15 00:00:00'),
                'is_express' => true,
                'tracking_id' => '550e8400-e29b-41d4-a716-446655440000',
                'weight' => 1.5,
                'metadata' => ['carrier' => 'acme', 'note' => 'a, b "c" d'],
            ]],
            'false boolean is not confused with null' => [[
                'label' => 'bulk',
                'item' => $item,
                'packed_at' => new \DateTimeImmutable('2024-01-15 10:30:00'),
                'delivered_at' => new \DateTimeImmutable('2024-01-15 10:30:00', new \DateTimeZone('UTC')),
                'dispatched_on' => new \DateTimeImmutable('2024-01-15 00:00:00'),
                'is_express' => false,
                'tracking_id' => '550e8400-e29b-41d4-a716-446655440000',
                'weight' => 0.0,
                'metadata' => [],
            ]],
            'all fields null' => [[
                'label' => null,
                'item' => null,
                'packed_at' => null,
                'delivered_at' => null,
                'dispatched_on' => null,
                'is_express' => null,
                'tracking_id' => null,
                'weight' => null,
                'metadata' => null,
            ]],
        ];
    }

    #[Test]
    public function decomposes_a_nested_composite_field(): void
    {
        [$tableName, $columnName] = $this->prepareTestTable($this->getTypeName());

        try {
            $this->connection->executeStatement(\sprintf(
                'INSERT INTO %s.%s ("%s") VALUES (ROW(\'bulk\', ROW(\'widget\', 42, 9.99)::%s.%s, NULL, NULL, NULL, NULL, NULL, NULL, NULL))',
                self::DATABASE_SCHEMA,
                $tableName,
                $columnName,
                self::DATABASE_SCHEMA,
                self::ITEM_TYPE_NAME
            ));

            $retrieved = $this->fetchConvertedValue($this->getTypeName(), $tableName, $columnName);

            $this->assertSame(
                [
                    'label' => 'bulk',
                    'item' => ['name' => 'widget', 'supplier_id' => 42, 'price' => '9.99'],
                    'packed_at' => null,
                    'delivered_at' => null,
                    'dispatched_on' => null,
                    'is_express' => null,
                    'tracking_id' => null,
                    'weight' => null,
                    'metadata' => null,
                ],
                $retrieved
            );
        } finally {
            $this->dropTestTableIfItExists($tableName);
        }
    }
}
