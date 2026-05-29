<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Every;
use PHPUnit\Framework\Attributes\Test;

class EveryTest extends BooleanTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'EVERY' => Every::class,
        ];
    }

    #[Test]
    public function returns_true_when_all_values_are_true(): void
    {
        $dql = 'SELECT EVERY(t.bool1) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsBooleans t';
        $result = $this->executeDqlQuery($dql);
        $this->assertTrue($result[0]['result']);
    }

    #[Test]
    public function returns_false_when_any_value_is_false(): void
    {
        $dql = 'SELECT EVERY(t.bool2) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsBooleans t';
        $result = $this->executeDqlQuery($dql);
        $this->assertFalse($result[0]['result']);
    }
}
