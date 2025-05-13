<?php

declare(strict_types=1);

use Rector\CodeQuality\Rector\Identical\FlipTypeControlToUseExclusiveTypeRector;
use Rector\Config\RectorConfig;
use Rector\Doctrine\Set\DoctrineSetList;
use Rector\Naming\Rector\Class_\RenamePropertyToMatchTypeRector;
use Rector\Set\ValueObject\LevelSetList;
use Rector\Set\ValueObject\SetList;

$basePath = __DIR__.'/../../';

return RectorConfig::configure()
    ->withPaths([
        $basePath.'ci',
        $basePath.'fixtures',
        $basePath.'src',
        $basePath.'tests',
    ])
    ->withCache($basePath.'var/cache/rector/')
    ->withParallel()
    ->withPHPStanConfigs([$basePath.'ci/phpstan/config.neon'])
    ->withComposerBased(
        doctrine: true,
        phpunit: true,
    )
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
    ->withSkip([
        RenamePropertyToMatchTypeRector::class,
        FlipTypeControlToUseExclusiveTypeRector::class => [
            $basePath.'src/MartinGeorgiev/Utils/DoctrineLexer.php',
        ],
    ])
    ->withImportNames(
        importNames: false,
        importDocBlockNames: false,
        importShortClasses: false,
        removeUnusedImports: true,
    );
