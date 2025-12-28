<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Sha384;
use PHPUnit\Framework\Attributes\Test;

class Sha384Test extends TextTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'SHA384' => Sha384::class,
        ];
    }

    #[Test]
    public function can_compute_sha384_of_a_string(): void
    {
        $dql = "SELECT SHA384('Hello Doctrine') as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t
                WHERE t.id = 1";

        $result = $this->executeDqlQuery($dql);
        $this->assertSame('\\x543cc1d2ea0e6d1ac3c438747d98eacc0ca8b119fcf5f33dc541d10137971522995a6b67def65e1b089cc1d7cd4d6533', $result[0]['result']);
    }

    #[Test]
    public function can_compute_sha384_of_text_field(): void
    {
        $dql = 'SELECT SHA384(t.text1) as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t
                WHERE t.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertSame('\\xf3a9019826c97a784c14a9c59be2c9940675a9312108cbede0ae2d686c0ae4b4f19b38841482120b97f0dfa49ec5680c', $result[0]['result']);
    }
}
