<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsTexts;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\UuidExtractVersion;

class UuidExtractVersionTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'UUID_EXTRACT_VERSION' => UuidExtractVersion::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'extracts version from uuid' => 'SELECT uuid_extract_version(c0_.text1) AS sclr_0 FROM ContainsTexts c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'extracts version from uuid' => \sprintf('SELECT UUID_EXTRACT_VERSION(e.text1) FROM %s e', ContainsTexts::class),
        ];
    }
}
