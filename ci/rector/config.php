<?php

declare(strict_types=1);

use Rector\CodingStyle\Rector\FuncCall\ArraySpreadInsteadOfArrayMergeRector;
use Rector\Config\RectorConfig;
use Rector\Doctrine\Set\DoctrineSetList;
use Rector\Naming\Rector\Class_\RenamePropertyToMatchTypeRector;
use Rector\Set\ValueObject\LevelSetList;
use Rector\Set\ValueObject\SetList;

return static function (RectorConfig $rectorConfig): void {
    $basePath = __DIR__.'/../../';
    $paths = [
        $basePath.'ci',
        $basePath.'src',
        $basePath.'tests',
    ];
    $rectorConfig->paths($paths);

    $rectorConfig->parallel();
    $rectorConfig->phpstanConfig($basePath.'ci/phpstan/config.neon');
    $rectorConfig->skip([
        ArraySpreadInsteadOfArrayMergeRector::class,
        RenamePropertyToMatchTypeRector::class,
    ]);
    $rectorConfig->importShortClasses(false);
    $rectorConfig->importNames(false, false); // @todo Enable once Rector introduces better support for function imports.

    $rectorConfig->import(SetList::CODE_QUALITY);
    $rectorConfig->import(SetList::DEAD_CODE);
    $rectorConfig->import(SetList::EARLY_RETURN);
    $rectorConfig->import(SetList::NAMING);
    $rectorConfig->import(SetList::PHP_80);
    $rectorConfig->import(SetList::PHP_81);
    $rectorConfig->import(SetList::TYPE_DECLARATION);

    $rectorConfig->import(DoctrineSetList::DOCTRINE_ORM_25);
    $rectorConfig->import(DoctrineSetList::DOCTRINE_CODE_QUALITY);

    $rectorConfig->import(LevelSetList::UP_TO_PHP_81);
};
