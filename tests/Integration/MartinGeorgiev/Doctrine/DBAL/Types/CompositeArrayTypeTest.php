<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Types\Type;
use Fixtures\MartinGeorgiev\Doctrine\ConcreteInventoryItemArrayType;
use Fixtures\MartinGeorgiev\Doctrine\ConcreteInventoryItemType;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

final class CompositeArrayTypeTest extends TestCase
{
    private const ITEM_TYPE_NAME = 'test_inventory_item';

    private const DBAL_TYPE_NAME = self::ITEM_TYPE_NAME.'[]';

    protected function setUp(): void
    {
        parent::setUp();

        $this->connection->executeStatement(\sprintf(
            'CREATE TYPE %s.%s AS (name text, supplier_id integer, price numeric)',
            self::DATABASE_SCHEMA,
            self::ITEM_TYPE_NAME
        ));

        $this->registerCompositeType(self::ITEM_TYPE_NAME, ConcreteInventoryItemType::class);
        $this->registerCompositeType(self::DBAL_TYPE_NAME, ConcreteInventoryItemArrayType::class);
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
     * @param array<int, array<string, mixed>|null> $phpValue
     */
    #[DataProvider('provideValidArrayTransformations')]
    #[Test]
    public function roundtrips_array_of_composites(array $phpValue, string $postgresValue): void
    {
        $typeName = $this->getTypeName();

        [$tableName, $columnName] = $this->prepareTestTable(self::DBAL_TYPE_NAME);

        try {
            $this->connection->createQueryBuilder()
                ->insert(self::DATABASE_SCHEMA.'.'.$tableName)
                ->values([$columnName => ':value'])
                ->setParameter('value', $phpValue, $typeName)
                ->executeStatement();

            $storedLiteral = $this->connection->fetchOne(\sprintf(
                'SELECT "%s"::text FROM %s.%s WHERE id = 1',
                $columnName,
                self::DATABASE_SCHEMA,
                $tableName
            ));
            $this->assertSame($postgresValue, $storedLiteral);

            $this->assertSame($phpValue, $this->fetchConvertedValue($typeName, $tableName, $columnName));
        } finally {
            $this->dropTestTableIfItExists($tableName);
        }
    }

    /**
     * @return array<string, array{phpValue: array<int, array<string, mixed>|null>, postgresValue: string}>
     */
    public static function provideValidArrayTransformations(): array
    {
        return [
            'several items' => [
                'phpValue' => [
                    ['name' => 'widget', 'supplier_id' => 1, 'price' => '9.99'],
                    ['name' => 'bolt', 'supplier_id' => 2, 'price' => '0.10'],
                ],
                'postgresValue' => '{"(widget,1,9.99)","(bolt,2,0.10)"}',
            ],
            'empty array' => [
                'phpValue' => [],
                'postgresValue' => '{}',
            ],
            'item with a delimiter needs escaping at both levels' => [
                'phpValue' => [['name' => 'a,b', 'supplier_id' => 1, 'price' => '1']],
                'postgresValue' => '{"(\\"a,b\\",1,1)"}',
            ],
            'item with quotes needs escaping at both levels' => [
                'phpValue' => [['name' => 'say "hi"', 'supplier_id' => 1, 'price' => '1']],
                'postgresValue' => '{"(\\"say \\"\\"hi\\"\\"\\",1,1)"}',
            ],
            'item with a null field' => [
                'phpValue' => [['name' => null, 'supplier_id' => 1, 'price' => '1']],
                'postgresValue' => '{"(,1,1)"}',
            ],
            'null element beside a value' => [
                'phpValue' => [['name' => 'x', 'supplier_id' => 1, 'price' => '1'], null],
                'postgresValue' => '{"(x,1,1)",NULL}',
            ],
        ];
    }
}
