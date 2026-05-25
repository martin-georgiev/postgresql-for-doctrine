<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Trgm;

use Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\TextTestCase;

abstract class TestCase extends TextTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->ensurePostgresExtensionInSchema('pg_trgm');
    }
}
