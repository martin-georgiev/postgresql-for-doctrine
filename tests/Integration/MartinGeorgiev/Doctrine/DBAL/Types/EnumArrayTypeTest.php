<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Types\Type;
use Fixtures\MartinGeorgiev\Doctrine\ConcreteTrickyLabelArrayType;
use Fixtures\MartinGeorgiev\Doctrine\Sizes;
use Fixtures\MartinGeorgiev\Doctrine\TrickyLabels;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidEnumArrayItemForDatabaseException;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidEnumArrayItemForPHPException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

final class EnumArrayTypeTest extends ArrayTypeTestCase
{
    private const DBAL_TYPE_NAME = 'test_tricky_label[]';

    /**
     * Valid in PostgreSQL but absent from the PHP enum, so it can stand in for DB/PHP model drift.
     */
    private const UNMAPPED_LABEL = 'unmapped';

    protected function setUp(): void
    {
        parent::setUp();

        // Built from the PHP enum so the PostgreSQL labels cannot drift away from the cases under test
        $labels = \implode(', ', \array_map(
            static fn (TrickyLabels $trickyLabels): string => "'".\str_replace("'", "''", $trickyLabels->value)."'",
            TrickyLabels::cases()
        ));
        $this->connection->executeStatement(\sprintf(
            "CREATE TYPE %s.test_tricky_label AS ENUM (%s, '%s')",
            self::DATABASE_SCHEMA,
            $labels,
            self::UNMAPPED_LABEL
        ));
        $this->registerType(self::DBAL_TYPE_NAME, ConcreteTrickyLabelArrayType::class);
    }

    /**
     * @param class-string<Type> $className
     */
    private function registerType(string $typeName, string $className): void
    {
        if (Type::hasType($typeName)) {
            Type::overrideType($typeName, $className);
        } else {
            Type::addType($typeName, $className);
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
     * @return array<string, array{array<int, TrickyLabels|null>}>
     */
    public static function provideValidTransformations(): array
    {
        return [
            'single case' => [[TrickyLabels::PLAIN]],
            'multiple cases' => [[TrickyLabels::PLAIN, TrickyLabels::WITH_SPACE]],
            'repeated cases' => [[TrickyLabels::WITH_SPACE, TrickyLabels::WITH_SPACE, TrickyLabels::PLAIN]],
            'cases mixed with a null element' => [[TrickyLabels::PLAIN, null, TrickyLabels::WITH_SPACE]],
            'only null elements' => [[null, null]],
        ];
    }

    #[DataProvider('provideLabelsNeedingEscaping')]
    #[Test]
    public function roundtrips_labels_needing_escaping(TrickyLabels $trickyLabels): void
    {
        $typeName = $this->getTypeName();
        $columnType = Type::getType($typeName)->getSQLDeclaration([], $this->connection->getDatabasePlatform());

        $this->runDbalBindingRoundTrip($typeName, $columnType, [$trickyLabels]);
    }

    /**
     * @return array<string, array{TrickyLabels}>
     */
    public static function provideLabelsNeedingEscaping(): array
    {
        return \array_combine(
            \array_map(static fn (TrickyLabels $trickyLabels): string => \strtolower($trickyLabels->name), TrickyLabels::cases()),
            \array_map(static fn (TrickyLabels $trickyLabels): array => [$trickyLabels], TrickyLabels::cases())
        );
    }

    #[Test]
    public function roundtrips_a_null_label_alongside_a_null_element(): void
    {
        $typeName = $this->getTypeName();
        $columnType = Type::getType($typeName)->getSQLDeclaration([], $this->connection->getDatabasePlatform());

        $this->runDbalBindingRoundTrip($typeName, $columnType, [TrickyLabels::UPPERCASE_NULL, null, TrickyLabels::LOWERCASE_NULL]);
    }

    #[Test]
    public function roundtrips_every_label_needing_escaping_in_a_single_array(): void
    {
        $typeName = $this->getTypeName();
        $columnType = Type::getType($typeName)->getSQLDeclaration([], $this->connection->getDatabasePlatform());

        $this->runDbalBindingRoundTrip($typeName, $columnType, TrickyLabels::cases());
    }

    #[DataProvider('provideInvalidItems')]
    #[Test]
    public function rejects_invalid_array_item(mixed $item): void
    {
        $this->expectException(InvalidEnumArrayItemForDatabaseException::class);

        $typeName = $this->getTypeName();
        $columnType = $this->getPostgresTypeName();

        $this->runDbalBindingRoundTrip($typeName, $columnType, [$item]);
    }

    /**
     * @return array<string, array{mixed}>
     */
    public static function provideInvalidItems(): array
    {
        return [
            'raw string matching an enum case' => ['plain'],
            'integer' => [42],
            'case of another enum' => [Sizes::SMALL],
        ];
    }

    #[Test]
    public function rejects_string_instead_of_array(): void
    {
        $this->expectException(InvalidEnumArrayItemForPHPException::class);

        $typeName = $this->getTypeName();
        $columnType = $this->getPostgresTypeName();

        $this->runDbalBindingRoundTrip($typeName, $columnType, 'plain');
    }

    #[Test]
    public function rejects_unknown_database_label(): void
    {
        // Simulating a DB/PHP model drift (e.g. a migration added a case that the PHP side missed)
        [$tableName, $columnName] = $this->prepareTestTable($this->getPostgresTypeName());

        try {
            $this->connection->executeStatement(
                \sprintf('INSERT INTO %s.%s ("%s") VALUES (?)', self::DATABASE_SCHEMA, $tableName, $columnName),
                [\sprintf('{plain,%s}', self::UNMAPPED_LABEL)]
            );

            $this->expectException(InvalidEnumArrayItemForPHPException::class);
            $this->fetchConvertedValue($this->getTypeName(), $tableName, $columnName);
        } finally {
            $this->dropTestTableIfItExists($tableName);
        }
    }
}
