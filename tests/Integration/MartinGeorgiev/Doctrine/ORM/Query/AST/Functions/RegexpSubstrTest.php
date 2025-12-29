<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\RegexpSubstr;
use PHPUnit\Framework\Attributes\Test;

class RegexpSubstrTest extends TextTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->requirePostgresVersion(150000, 'REGEXP_SUBSTR function');
    }

    protected function getStringFunctions(): array
    {
        return [
            'REGEXP_SUBSTR' => RegexpSubstr::class,
        ];
    }

    #[Test]
    public function can_extract_matching_substring(): void
    {
        $dql = "SELECT REGEXP_SUBSTR(t.text1, 'test') as result 
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t 
                WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertSame('test', $result[0]['result']);
    }

    #[Test]
    public function returns_null_when_no_match(): void
    {
        $dql = "SELECT REGEXP_SUBSTR(t.text1, 'xyz123') as result 
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t 
                WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertNull($result[0]['result']);
    }

    #[Test]
    public function can_extract_with_start_position(): void
    {
        $dql = "SELECT REGEXP_SUBSTR(t.text1, '[a-z]+', 1) as result 
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t 
                WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertNotNull($result[0]['result']);
    }
}
