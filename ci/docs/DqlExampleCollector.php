<?php

declare(strict_types=1);

namespace Ci\MartinGeorgiev\Docs;

use Ci\MartinGeorgiev\Shared\Repository;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\CommonMark\Node\Block\FencedCode;
use League\CommonMark\Parser\MarkdownParser;

final readonly class DqlExampleCollector
{
    private MarkdownParser $markdownParser;

    public function __construct(
        private Repository $repository,
    ) {
        $environment = new Environment();
        $environment->addExtension(new CommonMarkCoreExtension());

        $this->markdownParser = new MarkdownParser($environment);
    }

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
        foreach ($this->markdownParser->parse($this->repository->read($documentationFile))->iterator() as $node) {
            $isADqlFence = $node instanceof FencedCode && ($node->getInfoWords()[0] ?? '') === 'dql';
            if (!$isADqlFence) {
                continue;
            }

            $openingFenceLine = $node->getStartLine() ?? throw new \LogicException(\sprintf('A fence in %s has no line number.', $documentationFile));
            $bodyLines = \explode("\n", $node->getLiteral());
            $fenceBodies[] = \array_combine(\range($openingFenceLine + 1, $openingFenceLine + \count($bodyLines)), $bodyLines);
        }

        return $fenceBodies;
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
