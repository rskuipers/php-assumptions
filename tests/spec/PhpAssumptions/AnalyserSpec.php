<?php

namespace spec\PhpAssumptions;

use PhpAssumptions\Analyser;
use PhpAssumptions\Parser\NodeVisitor;
use PhpParser\Node;
use PhpParser\NodeTraverserInterface;
use PhpParser\ParserAbstract;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * @mixin Analyser
 */
class AnalyserSpec extends ObjectBehavior
{
    public function let(ParserAbstract $parser, NodeVisitor $nodeVisitor, NodeTraverserInterface $nodeTraverser)
    {
        $nodeTraverser->addVisitor($nodeVisitor)->shouldBeCalled();
        $this->beConstructedWith($parser, $nodeVisitor, $nodeTraverser);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(Analyser::class);
    }

    public function it_should_analyse_all_files(
        ParserAbstract $parser,
        NodeVisitor $nodeVisitor,
        NodeTraverserInterface $nodeTraverser,
        Node $node
    ) {
        $files = [fixture('MyClass.php')];
        $nodes = [$node];

        $nodeVisitor->setCurrentFile($files[0])->shouldBeCalled();

        $parser->parse(Argument::type('string'))->shouldBeCalled()->willReturn($nodes);

        $nodeTraverser->traverse($nodes)->shouldBeCalled();

        $this->analyse($files);
    }
}
