<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Sha512;
use PHPUnit\Framework\Attributes\Test;

class Sha512Test extends TextTestCase
{
    use ByteaAssertionTrait;

    protected function getStringFunctions(): array
    {
        return [
            'SHA512' => Sha512::class,
        ];
    }

    #[Test]
    public function can_compute_sha512_of_a_string(): void
    {
        $dql = "SELECT SHA512('Hello Doctrine') as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t
                WHERE t.id = 1";

        $result = $this->executeDqlQuery($dql);
        $this->assertByteaEquals('a7404495c235d30a9a2ba8766880c77e9b8f6b2187633e5d3b8054c14b6c9cfb134f1cbc72b40b165826f12579235c691bcb4f7f2a37d8e88ef31555fdf1f0c6', $result[0]['result']);
    }

    #[Test]
    public function can_compute_sha512_of_text_field(): void
    {
        $dql = 'SELECT SHA512(t.text1) as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t
                WHERE t.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertByteaEquals('c240dd0b1a9b00c2478ab95f2184c81d0f3f923a751c71e61af36bb34fe9f240399ca3af2f061cbc1da2535ce93f6bcedead90cad16f14346cd34f394ee02f5e', $result[0]['result']);
    }
}
