<?php

namespace unit\PhpAssumptions\Parser;

use PhpAssumptions\Detector;
use PhpAssumptions\Output\OutputInterface;
use PhpAssumptions\Parser\NodeVisitor;
use PhpParser\Node;
use PhpParser\PrettyPrinter\Standard;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTestCase;

class NodeVisitorTest extends ProphecyTestCase
{
    /**
     * @var NodeVisitor
     */
    private $nodeVisitor;

    /**
     * @var OutputInterface
     */
    private $output;

    /**
     * @var Standard
     */
    private $prettyPrinter;

    /**
     * @var Detector
     */
    private $detector;

    /**
     * @var Node
     */
    private $node;

    public function setUp()
    {
        $this->output = $this->prophesize(OutputInterface::class);
        $this->prettyPrinter = $this->prophesize(Standard::class);
        $this->detector = $this->prophesize(Detector::class);
        $this->node = $this->prophesize(Node::class);
        $this->nodeVisitor = new NodeVisitor(
            $this->output->reveal(),
            $this->prettyPrinter->reveal(),
            $this->detector->reveal()
        );
    }

    /**
     * @test
     */
    public function itShouldCallScanAndWriteOnSuccess()
    {
        $this->node->getLine()->shouldBeCalled()->willReturn(120);

        $this->prettyPrinter->prettyPrint([$this->node])->shouldBeCalled()->willReturn('Weak assumption');

        $this->detector->scan($this->node)->shouldBeCalled()->willReturn(true);
        $this->output->write('MyClass.php', 120, 'Weak assumption')->shouldBeCalled();

        $this->nodeVisitor->setCurrentFile('MyClass.php');
        $this->nodeVisitor->leaveNode($this->node->reveal());
    }

    /**
     * @test
     */
    public function itShouldCallScanAndNotWriteOnFailure()
    {
        $this->detector->scan($this->node)->shouldBeCalled()->willReturn(false);
        $this->output->write(Argument::any(), Argument::any(), Argument::any())->shouldNotBeCalled();
        $this->nodeVisitor->leaveNode($this->node->reveal());
    }
}
