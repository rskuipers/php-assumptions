<?php

namespace PhpAssumptions;

use PhpAssumptions\Output\OutputInterface;
use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;
use PhpParser\PrettyPrinter\Standard;

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
     * @var Standard
     */
    private $prettyPrinter;

    /**
     * @param OutputInterface $output
     */
    public function __construct(OutputInterface $output)
    {
        $this->prettyPrinter = new Standard();
        $this->output = $output;
    }

    /**
     * @param Node $node
     * @return false|null|Node|\PhpParser\Node[]|void
     */
    public function leaveNode(Node $node) {
        if ($node instanceof Node\Stmt\If_
            && ($node->cond instanceof Node\Expr\BinaryOp\NotIdentical || $node->cond instanceof Node\Expr\BinaryOp\NotEqual)
        ) {
            $cond = $node->cond;
            if ($cond->left instanceof Node\Expr\Variable && $cond->right instanceof Node\Expr\ConstFetch
                || $cond->right instanceof Node\Expr\Variable && $cond->left instanceof Node\Expr\ConstFetch
            ) {
                $this->output->write(
                    $this->currentFile,
                    $node->getLine(),
                    explode("\n", $this->prettyPrinter->prettyPrint([$node]))[0]
                );
            }
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
