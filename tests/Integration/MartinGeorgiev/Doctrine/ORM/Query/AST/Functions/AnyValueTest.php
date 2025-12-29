<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\AnyValue;
use PHPUnit\Framework\Attributes\Test;

class AnyValueTest extends TextTestCase
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
    public function can_get_any_value_from_group(): void
    {
        $dql = 'SELECT ANY_VALUE(t.text1) as result 
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t';
        $result = $this->executeDqlQuery($dql);
        $this->assertNotNull($result[0]['result']);
    }
}
