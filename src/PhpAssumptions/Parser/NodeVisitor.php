<?php

namespace PhpAssumptions\Parser;

use PhpAssumptions\Detector;
use PhpAssumptions\Output\OutputInterface;
use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;
use PhpParser\PrettyPrinterAbstract;

class NodeVisitor extends NodeVisitorAbstract
{
    /**
     * @var OutputInterface
     */
    private $output;

    /**
     * @var string
     */
    private $currentFile;

    /**
     * @var PrettyPrinterAbstract
     */
    private $prettyPrinter;

    /**
     * @var Detector
     */
    private $detector;

    /**
     * @param OutputInterface $output
     * @param PrettyPrinterAbstract $prettyPrinter
     * @param Detector $detector
     */
    public function __construct(OutputInterface $output, PrettyPrinterAbstract $prettyPrinter, Detector $detector)
    {
        $this->prettyPrinter = $prettyPrinter;
        $this->output = $output;
        $this->detector = $detector;
    }

    /**
     * @param Node $node
     * @return false|null|Node|\PhpParser\Node[]|void
     */
    public function leaveNode(Node $node)
    {
        if ($this->detector->scan($node)) {
            $this->output->write(
                $this->currentFile,
                $node->getLine(),
                explode("\n", $this->prettyPrinter->prettyPrint([$node]))[0]
            );
        }
    }

    /**
     * @param string $currentFile
     */
    public function setCurrentFile($currentFile)
    {
        $this->currentFile = $currentFile;
    }
}
