<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BoolOr;
use PHPUnit\Framework\Attributes\Test;

final class BoolOrTest extends BooleanTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'BOOL_OR' => BoolOr::class,
        ];
    }

    #[Test]
    public function returns_the_boolean_disjunction_from_an_entity_field(): void
    {
        $dql = 'SELECT BOOL_OR(t.bool2) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsBooleans t';
        $result = $this->executeDqlQuery($dql);
        $this->assertTrue($result[0]['result']);
    }
}
