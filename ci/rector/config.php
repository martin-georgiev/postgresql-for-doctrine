<?php

declare(strict_types=1);

use Rector\CodeQuality\Rector\Identical\FlipTypeControlToUseExclusiveTypeRector;
use Rector\Config\RectorConfig;
use Rector\Doctrine\Set\DoctrineSetList;
use Rector\Naming\Rector\Class_\RenamePropertyToMatchTypeRector;
use Rector\Set\ValueObject\LevelSetList;
use Rector\Set\ValueObject\SetList;

return static function (RectorConfig $rectorConfig): void {
    $basePath = __DIR__.'/../../';
    $paths = [
        $basePath.'ci',
        $basePath.'fixtures',
        $basePath.'src',
        $basePath.'tests',
    ];
    $rectorConfig->paths($paths);

    $rectorConfig->cacheDirectory($basePath.'var/cache/rector/');

    $rectorConfig->parallel();
    $rectorConfig->phpstanConfig($basePath.'ci/phpstan/config.neon');

    $rectorConfig->sets([
        SetList::CODE_QUALITY,
        SetList::DEAD_CODE,
        SetList::EARLY_RETURN,
        SetList::NAMING,
        SetList::PHP_80,
        SetList::PHP_81,
        SetList::TYPE_DECLARATION,
        SetList::PRIVATIZATION,
        SetList::CODING_STYLE,
        DoctrineSetList::DOCTRINE_ORM_25,
        DoctrineSetList::DOCTRINE_CODE_QUALITY,
        LevelSetList::UP_TO_PHP_81,
    ]);

    $rectorConfig->skip([
        RenamePropertyToMatchTypeRector::class,
        FlipTypeControlToUseExclusiveTypeRector::class => [
            $basePath.'src/MartinGeorgiev/Utils/DoctrineLexer.php',
        ],
    ]);

    $rectorConfig->importShortClasses(false);
    $rectorConfig->importNames(false, false);
};
