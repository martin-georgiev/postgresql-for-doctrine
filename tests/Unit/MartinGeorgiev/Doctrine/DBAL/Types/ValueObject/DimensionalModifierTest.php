<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\DBAL\Types\ValueObject;

use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\DimensionalModifier;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class DimensionalModifierTest extends TestCase
{
    #[DataProvider('provideValidDimensionalModifiers')]
    #[Test]
    public function can_create_from_string(string $modifierString, DimensionalModifier $dimensionalModifier): void
    {
        $modifier = DimensionalModifier::from($modifierString);

        $this->assertSame($dimensionalModifier, $modifier);
        $this->assertSame($modifierString, $modifier->value);
    }

    #[Test]
    public function throws_exception_for_invalid_modifier(): void
    {
        $this->expectException(\ValueError::class);

        DimensionalModifier::from('INVALID');
    }

    #[DataProvider('provideValidDimensionalModifiers')]
    #[Test]
    public function returns_enum_for_valid_modifiers(string $modifierString, DimensionalModifier $dimensionalModifier): void
    {
        $result = DimensionalModifier::tryFrom($modifierString);

        $this->assertSame($dimensionalModifier, $result);
    }

    /**
     * @return array<string, array{string, DimensionalModifier}>
     */
    public static function provideValidDimensionalModifiers(): array
    {
        return [
            'z dimension' => ['Z', DimensionalModifier::Z],
            'm dimension' => ['M', DimensionalModifier::M],
            'zm dimensions' => ['ZM', DimensionalModifier::ZM],
        ];
    }

    #[Test]
    public function returns_null_for_invalid_modifiers(): void
    {
        $this->assertNull(DimensionalModifier::tryFrom('INVALID_MODIFIER'));
        $this->assertNull(DimensionalModifier::tryFrom(''));
        $this->assertNull(DimensionalModifier::tryFrom('X'));
        $this->assertNull(DimensionalModifier::tryFrom('Y'));
    }
}
