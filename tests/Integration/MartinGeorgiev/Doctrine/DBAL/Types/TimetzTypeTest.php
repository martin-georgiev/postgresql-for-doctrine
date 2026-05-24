<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\DBAL\Types;

use PHPUnit\Framework\Attributes\Test;

class TimetzTypeTest extends ScalarTypeTestCase
{
    protected function getTypeName(): string
    {
        return 'timetz';
    }

    #[Test]
    public function can_handle_time_with_timezone(): void
    {
        $this->runDbalBindingRoundTrip($this->getTypeName(), $this->getPostgresTypeName(), '10:30:00+00');
    }

    #[Test]
    public function can_handle_time_with_positive_offset(): void
    {
        $this->runDbalBindingRoundTrip($this->getTypeName(), $this->getPostgresTypeName(), '14:45:00+02');
    }
}
