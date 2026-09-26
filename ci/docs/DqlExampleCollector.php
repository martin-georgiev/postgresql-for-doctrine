<?php

declare(strict_types=1);

namespace MartinGeorgiev\Docs;

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
        foreach ($this->repository->documentationFiles() as $documentationFile) {
            $isInsideADqlFence = false;
            $statementStartLine = 0;
            $statementLines = [];
            foreach ($this->repository->readLines($documentationFile) as $index => $line) {
                $isAFenceMarker = \preg_match('/^\s*```(?<language>\w*)\s*$/', $line, $fence) === 1;
                $endsAStatement = $isInsideADqlFence && (\trim($line) === '' || ($isAFenceMarker && $fence['language'] === ''));
                if ($endsAStatement && $statementLines !== []) {
                    $statements[] = new DqlExample($documentationFile.':'.$statementStartLine, \implode("\n", $statementLines));
                    $statementLines = [];
                }

                if ($isAFenceMarker) {
                    $isInsideADqlFence = !$isInsideADqlFence && $fence['language'] === 'dql';

                    continue;
                }

                $isACaption = \str_starts_with(\ltrim($line), '--');
                if ($isInsideADqlFence && \trim($line) !== '' && !$isACaption) {
                    if ($statementLines === []) {
                        $statementStartLine = $index + 1;
                    }

                    $statementLines[] = \rtrim($line);
                }
            }

            $endsInsideAnUnclosedFence = $statementLines !== [];
            if ($endsInsideAnUnclosedFence) {
                $statements[] = new DqlExample($documentationFile.':'.$statementStartLine, \implode("\n", $statementLines));
            }
        }

        return $statements;
    }
}
