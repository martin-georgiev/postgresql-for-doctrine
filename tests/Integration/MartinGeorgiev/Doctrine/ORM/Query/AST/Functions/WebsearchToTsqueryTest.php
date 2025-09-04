<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Doctrine\DBAL\Exception\DriverException;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\WebsearchToTsquery;
use PHPUnit\Framework\Attributes\Test;

class WebsearchToTsqueryTest extends TextTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'websearch_to_tsquery' => WebsearchToTsquery::class,
        ];
    }

    #[Test]
    public function websearch_to_tsquery_with_explicit_config(): void
    {
        $dql = "SELECT websearch_to_tsquery('english', '\"sad cat\" or \"fat rat\"') as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertSame("'sad' <-> 'cat' | 'fat' <-> 'rat'", $result[0]['result']);
    }

    #[Test]
    public function websearch_to_tsquery_with_default_config(): void
    {
        $dql = "SELECT websearch_to_tsquery('\"sad cat\" or \"fat rat\"') as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertSame("'sad' <-> 'cat' | 'fat' <-> 'rat'", $result[0]['result']);
    }

    #[Test]
    public function totsquery_throws_with_invalid_input(): void
    {
        $this->expectException(DriverException::class);
        $this->expectExceptionMessageMatches('/text search configuration .*invalid_regconfig.* does not exist/i');
        $dql = "SELECT websearch_to_tsquery('invalid_regconfig', 'foo') as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t WHERE t.id = 1";
        $this->executeDqlQuery($dql);
    }
}
