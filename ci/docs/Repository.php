<?php

declare(strict_types=1);

namespace MartinGeorgiev\Docs;

final readonly class Repository
{
    /**
     * @var string
     */
    public const FUNCTION_SOURCE_DIRECTORY = 'src/MartinGeorgiev/Doctrine/ORM/Query/AST/Functions';

    /**
     * @var string
     */
    public const FUNCTION_SOURCE_NAMESPACE = 'MartinGeorgiev\\Doctrine\\ORM\\Query\\AST\\Functions\\';

    /**
     * @var list<string>
     */
    private const NON_FUNCTION_SOURCE_SUBFOLDERS = ['Exception', 'Traits'];

    public string $rootDirectory;

    public function __construct()
    {
        $this->rootDirectory = \dirname(__DIR__, 2);
    }

    public function read(string $relativePath): string
    {
        $contents = \file_get_contents($this->rootDirectory.'/'.$relativePath);
        if (!\is_string($contents)) {
            throw new \RuntimeException(\sprintf('Could not read %s.', $relativePath));
        }

        return $contents;
    }

    /**
     * @return list<string>
     */
    public function readLines(string $relativePath): array
    {
        return \explode("\n", \str_replace("\r\n", "\n", $this->read($relativePath)));
    }

    /**
     * @return list<string> relative to the repository root
     */
    public function phpFilesIn(string $relativeDirectory): array
    {
        $files = [];
        foreach (new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($this->rootDirectory.'/'.$relativeDirectory, \FilesystemIterator::SKIP_DOTS)) as $file) {
            if ($file instanceof \SplFileInfo && $file->getExtension() === 'php') {
                $files[] = \substr($file->getPathname(), \strlen($this->rootDirectory) + 1);
            }
        }

        \sort($files);

        return $files;
    }

    public function functionClassIn(string $relativePath): string
    {
        $pathInFunctionSource = \substr($relativePath, \strlen(self::FUNCTION_SOURCE_DIRECTORY) + 1, -4);

        return self::FUNCTION_SOURCE_NAMESPACE.\str_replace('/', '\\', $pathInFunctionSource);
    }

    /**
     * @return list<string> sorted
     */
    public function concreteFunctionClasses(): array
    {
        $functionClasses = [];
        foreach ($this->phpFilesIn(self::FUNCTION_SOURCE_DIRECTORY) as $relativePath) {
            $functionClass = $this->functionClassIn($relativePath);
            $subfolder = \strtok(\substr($functionClass, \strlen(self::FUNCTION_SOURCE_NAMESPACE)), '\\');
            $isABuildingBlock = \in_array($subfolder, self::NON_FUNCTION_SOURCE_SUBFOLDERS, true);
            if (!$isABuildingBlock && \class_exists($functionClass) && !(new \ReflectionClass($functionClass))->isAbstract()) {
                $functionClasses[] = $functionClass;
            }
        }

        \sort($functionClasses);

        return $functionClasses;
    }

    /**
     * @return list<string>
     */
    public function documentationFiles(): array
    {
        $documentationFiles = \array_map(
            fn (string $path): string => \substr($path, \strlen($this->rootDirectory) + 1),
            \glob($this->rootDirectory.'/docs/*.md') ?: []
        );

        return [...$documentationFiles, 'README.md'];
    }
}
