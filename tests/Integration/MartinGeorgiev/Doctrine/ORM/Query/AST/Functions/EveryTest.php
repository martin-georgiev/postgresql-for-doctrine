<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Every;
use PHPUnit\Framework\Attributes\Test;

final class EveryTest extends BooleanTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'EVERY' => Every::class,
        ];
    }

    #[Test]
    public function returns_the_boolean_conjunction_from_an_entity_field(): void
    {
        $dql = 'SELECT EVERY(t.bool1) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsBooleans t';
        $result = $this->executeDqlQuery($dql);
        $this->assertTrue($result[0]['result']);
    }
}
