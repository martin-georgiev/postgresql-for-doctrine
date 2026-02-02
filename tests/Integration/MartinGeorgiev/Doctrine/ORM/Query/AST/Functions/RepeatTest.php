<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Repeat;
use PHPUnit\Framework\Attributes\Test;

class RepeatTest extends TextTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'REPEAT' => Repeat::class,
        ];
    }

    #[Test]
    public function can_repeat_string(): void
    {
        $dql = 'SELECT REPEAT(t.text1, 2) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsTexts t 
                WHERE t.id = 3';

        $result = $this->executeDqlQuery($dql);
        $this->assertIsString($result[0]['result']);
        $this->assertSame('foofoo', $result[0]['result']);
    }
}
