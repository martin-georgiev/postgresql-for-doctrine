<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Rtrim;
use PHPUnit\Framework\Attributes\Test;

final class RtrimTest extends TextTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'RTRIM' => Rtrim::class,
        ];
    }

    #[Test]
    public function returns_the_right_trimmed_value_from_a_literal(): void
    {
        $dql = "SELECT RTRIM('hello  ') as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t
                WHERE t.id = 1";

        $result = $this->executeDqlQuery($dql);
        $this->assertSame('hello', $result[0]['result']);
    }

    #[Test]
    public function returns_the_right_trimmed_value_from_an_entity_field(): void
    {
        $dql = "SELECT RTRIM(t.text1, 'fobar') as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t
                WHERE t.id = 3";

        $result = $this->executeDqlQuery($dql);
        $this->assertSame('', $result[0]['result']);
    }
}
