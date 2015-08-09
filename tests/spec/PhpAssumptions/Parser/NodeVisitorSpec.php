<?php

namespace spec\PhpAssumptions;

use PhpAssumptions\Detector;
use PhpAssumptions\Parser\NodeVisitor;
use PhpAssumptions\Output\OutputInterface;
use PhpParser\PrettyPrinterAbstract;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * @mixin NodeVisitor
 */
class NodeVisitorSpec extends ObjectBehavior
{
    public function let(OutputInterface $output, PrettyPrinterAbstract $prettyPrinter, Detector $detector)
    {
        $this->beConstructedWith($output, $prettyPrinter, $detector);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(NodeVisitor::class);
    }
}
