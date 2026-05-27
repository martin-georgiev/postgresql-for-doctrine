<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Tests\Integration\MartinGeorgiev\TestCase;

abstract class XmlTestCase extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->createTestTableForXmlFixture();
        $this->insertTestDataForXmlFixture();
    }

    protected function createTestTableForXmlFixture(): void
    {
        $tableName = 'containsxml';

        $this->createTestSchema();
        $this->dropTestTableIfItExists($tableName);

        $fullTableName = \sprintf('%s.%s', self::DATABASE_SCHEMA, $tableName);
        $sql = \sprintf('
            CREATE TABLE %s (
                id SERIAL PRIMARY KEY,
                content XML
            )
        ', $fullTableName);

        $this->connection->executeStatement($sql);
    }

    protected function insertTestDataForXmlFixture(): void
    {
        $sql = \sprintf("
            INSERT INTO %s.containsxml (content) VALUES
            ('<root><item>foo</item><item>bar</item></root>'),
            ('<catalog><product><name>test</name></product></catalog>')
        ", self::DATABASE_SCHEMA);
        $this->connection->executeStatement($sql);
    }
}
