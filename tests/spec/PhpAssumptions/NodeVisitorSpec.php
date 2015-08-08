<?php

namespace spec\PhpAssumptions;

use PhpAssumptions\NodeVisitor;
use PhpAssumptions\Output\OutputInterface;
use PhpParser\PrettyPrinterAbstract;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * @mixin NodeVisitor
 */
class NodeVisitorSpec extends ObjectBehavior
{
    public function let(OutputInterface $output, PrettyPrinterAbstract $prettyPrinter)
    {
        $this->beConstructedWith($output, $prettyPrinter);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(NodeVisitor::class);
    }
}
