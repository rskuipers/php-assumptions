<?php

namespace PhpAssumptions\Parser;

use PhpAssumptions\Analyser;
use PhpAssumptions\Detector;
use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;
use PhpParser\PrettyPrinterAbstract;

class NodeVisitor extends NodeVisitorAbstract
{
    /**
     * @var Analyser
     */
    private $analyser;

    /**
     * @var PrettyPrinterAbstract
     */
    private $prettyPrinter;

    /**
     * @var Detector
     */
    private $detector;

    /**
     * @param Analyser $analyser
     * @param PrettyPrinterAbstract $prettyPrinter
     * @param Detector $detector
     */
    public function __construct(Analyser $analyser, PrettyPrinterAbstract $prettyPrinter, Detector $detector)
    {
        $this->prettyPrinter = $prettyPrinter;
        $this->analyser = $analyser;
        $this->detector = $detector;
    }

    /**
     * @param Node $node
     * @return false|null|Node|\PhpParser\Node[]|void
     */
    public function enterNode(Node $node)
    {
        if ($this->detector->isBoolExpression($node)) {
            $this->analyser->foundBoolExpression();
        }

        if ($this->detector->scan($node)) {
            $this->analyser->foundAssumption(
                $node->getLine(),
                explode("\n", $this->prettyPrinter->prettyPrint([$node]))[0]
            );
        }
    }
}
