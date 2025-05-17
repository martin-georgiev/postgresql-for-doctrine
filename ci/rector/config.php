<?php

declare(strict_types=1);

use Rector\CodeQuality\Rector\Identical\FlipTypeControlToUseExclusiveTypeRector;
use Rector\Config\RectorConfig;
use Rector\Doctrine\Set\DoctrineSetList;
use Rector\Naming\Rector\Class_\RenamePropertyToMatchTypeRector;
use Rector\Set\ValueObject\LevelSetList;
use Rector\Set\ValueObject\SetList;

// Using the new configuration pattern with RectorConfig::configure()
// See: https://getrector.com/blog/introducing-composer-version-based-sets
return RectorConfig::configure()
    // Define paths to be processed
    ->withPaths([
        __DIR__.'/../../ci',
        __DIR__.'/../../fixtures',
        __DIR__.'/../../src',
        __DIR__.'/../../tests',
    ])
    // Cache directory for better performance
    ->withCache(__DIR__.'/../../var/cache/rector/')
    // Enable parallel processing
    ->withParallel()
    // PHPStan configuration
    ->withPHPStanConfigs([__DIR__.'/../../ci/phpstan/config.neon'])
    // Use composer-based sets for Doctrine
    // This replaces the deprecated DOCTRINE_ORM_25 constant
    ->withComposerBased(
        doctrine: true,
        phpunit: true
    )
    // Standard sets to apply
    ->withSets([
        SetList::CODE_QUALITY,
        SetList::DEAD_CODE,
        SetList::EARLY_RETURN,
        SetList::NAMING,
        SetList::PHP_80,
        SetList::PHP_81,
        SetList::TYPE_DECLARATION,
        SetList::PRIVATIZATION,
        SetList::CODING_STYLE,
        DoctrineSetList::DOCTRINE_CODE_QUALITY,
        LevelSetList::UP_TO_PHP_81,
    ])
    // Rules to skip
    ->withSkip([
        RenamePropertyToMatchTypeRector::class,
        FlipTypeControlToUseExclusiveTypeRector::class => [
            __DIR__.'/../../src/MartinGeorgiev/Utils/DoctrineLexer.php',
        ],
    ])
    // Import configuration
    ->withImportNames(false, false, true);
