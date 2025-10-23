<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsTexts;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\UuidExtractTimestamp;

class UuidExtractTimestampTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'UUID_EXTRACT_TIMESTAMP' => UuidExtractTimestamp::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'extracts timestamp from uuid' => 'SELECT uuid_extract_timestamp(c0_.text1) AS sclr_0 FROM ContainsTexts c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'extracts timestamp from uuid' => \sprintf('SELECT UUID_EXTRACT_TIMESTAMP(e.text1) FROM %s e', ContainsTexts::class),
        ];
    }
}
