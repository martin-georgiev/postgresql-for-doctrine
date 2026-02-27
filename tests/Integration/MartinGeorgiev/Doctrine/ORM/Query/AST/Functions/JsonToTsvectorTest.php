<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonToTsvector;
use PHPUnit\Framework\Attributes\Test;

class JsonToTsvectorTest extends TextTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'JSON_TO_TSVECTOR' => JsonToTsvector::class,
        ];
    }

    #[Test]
    public function can_convert_json_to_tsvector(): void
    {
        $dql = "SELECT JSON_TO_TSVECTOR('{\"title\": \"lorem ipsum\"}', '[\"string\"]') as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertSame("'ipsum':2 'lorem':1", $result[0]['result']);
    }

    #[Test]
    public function can_convert_json_to_tsvector_with_config(): void
    {
        $dql = "SELECT JSON_TO_TSVECTOR('english', '{\"body\": \"lorem ipsum dolor\"}', '[\"string\"]') as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertSame("'dolor':3 'ipsum':2 'lorem':1", $result[0]['result']);
    }
}
