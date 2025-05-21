<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Tests\Integration\MartinGeorgiev\TestCase;

abstract class DateTestCase extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->createTestTableForDateFixture();
        $this->insertTestDataForDateFixture();
    }

    protected function createTestTableForDateFixture(): void
    {
        $sql = 'CREATE TABLE IF NOT EXISTS containsdates (
            id SERIAL PRIMARY KEY,
            date1 DATE,
            date2 DATE,
            datetime1 TIMESTAMP,
            datetime2 TIMESTAMP,
            time1 TIME,
            time2 TIME
        )';
        $this->entityManager->getConnection()->executeStatement($sql);
    }

    protected function insertTestDataForDateFixture(): void
    {
        $sql = "INSERT INTO containsdates (date1, date2, datetime1, datetime2, time1, time2) 
                VALUES ('2023-06-15', '2023-06-16', '2023-06-15 10:30:00', '2023-06-16 11:45:00', '10:30:00', '11:45:00')";
        $this->entityManager->getConnection()->executeStatement($sql);
    }
}
