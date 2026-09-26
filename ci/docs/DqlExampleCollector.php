<?php

declare(strict_types=1);

namespace Ci\MartinGeorgiev\Docs;

use Ci\MartinGeorgiev\Shared\Repository;

final readonly class DqlExampleCollector
{
    public function __construct(
        private Repository $repository,
    ) {}

    /**
     * @return list<DqlExample>
     */
    public function docblockExamples(): array
    {
        $examples = [];
        foreach ($this->repository->phpFilesIn(Repository::FUNCTION_SOURCE_DIRECTORY) as $relativePath) {
            foreach ($this->repository->readLines($relativePath) as $index => $line) {
                if (!\str_contains($line, '@example')) {
                    continue;
                }

                $quotedDql = \preg_match('/@example[^"]*"(?<dql>.*)"/', $line, $match) === 1 ? \str_replace('\"', '"', $match['dql']) : '';
                $examples[] = new DqlExample($relativePath.':'.($index + 1), $quotedDql, $this->repository->functionClassIn($relativePath));
            }
        }

        return $examples;
    }

    /**
     * @return list<DqlExample>
     */
    public function dqlFenceStatements(): array
    {
        $statements = [];
        foreach ($this->documentationFiles() as $documentationFile) {
            foreach ($this->dqlFenceBodiesIn($documentationFile) as $fenceBody) {
                foreach ($this->paragraphsOf($fenceBody) as $statementLines) {
                    $statements[] = new DqlExample($documentationFile.':'.\array_key_first($statementLines), \implode("\n", $statementLines));
                }
            }
        }

        return $statements;
    }

    /**
     * @return list<string>
     */
    private function documentationFiles(): array
    {
        $documentationFiles = \array_map(
            fn (string $path): string => \substr($path, \strlen($this->repository->rootDirectory) + 1),
            \glob($this->repository->rootDirectory.'/docs/*.md') ?: []
        );

        return [...$documentationFiles, 'README.md'];
    }

    /**
     * @return list<array<int, string>> each fence's lines, keyed by line number
     */
    private function dqlFenceBodiesIn(string $documentationFile): array
    {
        $fenceBodies = [];
        $openDqlFenceBody = null;
        foreach ($this->repository->readLines($documentationFile) as $index => $line) {
            $fenceLanguage = $this->fenceLanguageOf($line);
            if ($fenceLanguage === null) {
                if ($openDqlFenceBody !== null) {
                    $openDqlFenceBody[$index + 1] = $line;
                }

                continue;
            }

            if ($openDqlFenceBody !== null) {
                $fenceBodies[] = $openDqlFenceBody;
                $openDqlFenceBody = null;

                continue;
            }

            if ($fenceLanguage === 'dql') {
                $openDqlFenceBody = [];
            }
        }

        $endsInsideAnUnclosedDqlFence = $openDqlFenceBody !== null;
        if ($endsInsideAnUnclosedDqlFence) {
            $fenceBodies[] = $openDqlFenceBody;
        }

        return $fenceBodies;
    }

    private function fenceLanguageOf(string $line): ?string
    {
        return \preg_match('/^\s*```(?<language>\w*)\s*$/', $line, $fence) === 1 ? $fence['language'] : null;
    }

    /**
     * @param array<int, string> $fenceBody
     *
     * @return list<non-empty-array<int, string>>
     */
    private function paragraphsOf(array $fenceBody): array
    {
        $paragraphs = [];
        $currentParagraph = [];
        foreach ($fenceBody as $lineNumber => $line) {
            if (\trim($line) === '') {
                $paragraphs[] = $currentParagraph;
                $currentParagraph = [];

                continue;
            }

            $isACaption = \str_starts_with(\ltrim($line), '--');
            if (!$isACaption) {
                $currentParagraph[$lineNumber] = \rtrim($line);
            }
        }

        $paragraphs[] = $currentParagraph;

        return \array_values(\array_filter($paragraphs));
    }
}
