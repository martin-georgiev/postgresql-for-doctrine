<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\AnyValue;
use PHPUnit\Framework\Attributes\Test;

final class AnyValueTest extends TextTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->requirePostgresVersion(160000, 'ANY_VALUE function');
    }

    protected function getStringFunctions(): array
    {
        return [
            'ANY_VALUE' => AnyValue::class,
        ];
    }

    #[Test]
    public function returns_an_arbitrary_value_from_an_entity_field(): void
    {
        $dql = 'SELECT ANY_VALUE(t.text1) as result 
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t
                WHERE t.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertSame('this is a test string', $result[0]['result']);
    }
}
