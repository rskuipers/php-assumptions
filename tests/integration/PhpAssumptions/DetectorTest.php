<?php

namespace integration\PhpAssumptions;

use PhpAssumptions\Detector;
use PhpParser\Lexer;
use PhpParser\Parser;
use Prophecy\PhpUnit\ProphecyTestCase;

class NodeVisitorTest extends ProphecyTestCase
{
    /**
     * @var Parser
     */
    private $parser;

    /**
     * @var Detector
     */
    private $detector;

    public function setUp()
    {
        $this->parser = new Parser(new Lexer());
        $this->detector = new Detector();
    }

    /**
     * @test
     */
    public function itShouldDetectNotNull()
    {
        $node = $this->parser->parse('<?php $var !== null;')[0];
        $this->assertTrue($this->detector->scan($node));

        $node = $this->parser->parse('<?php null !== $var;')[0];
        $this->assertTrue($this->detector->scan($node));
    }

    /**
     * @test
     */
    public function itShouldDetectEqualsNotFalse()
    {
        $node = $this->parser->parse('<?php $test !== false;')[0];
        $this->assertTrue($this->detector->scan($node));

        $node = $this->parser->parse('<?php false !== $test;')[0];
        $this->assertTrue($this->detector->scan($node));

        $node = $this->parser->parse('<?php false != $test;')[0];
        $this->assertTrue($this->detector->scan($node));

        $node = $this->parser->parse('<?php $test != false;')[0];
        $this->assertTrue($this->detector->scan($node));

        $node = $this->parser->parse('<?php !$test;')[0];
        $this->assertTrue($this->detector->scan($node));
    }

    /**
     * @test
     */
    public function itShouldDetectEqualsTrue()
    {
        $node = $this->parser->parse('<?php $test !== true;')[0];
        $this->assertTrue($this->detector->scan($node));

        $node = $this->parser->parse('<?php true !== $test;')[0];
        $this->assertTrue($this->detector->scan($node));

        $node = $this->parser->parse('<?php true != $test;')[0];
        $this->assertTrue($this->detector->scan($node));

        $node = $this->parser->parse('<?php $test != true;')[0];
        $this->assertTrue($this->detector->scan($node));

        $node = $this->parser->parse('<?php $test ? "" : "";')[0];
        $this->assertTrue($this->detector->scan($node));
    }

    /**
     * @test
     */
    public function itShouldDetectEqualsScalar()
    {
        $node = $this->parser->parse('<?php $test == "test";')[0];
        $this->assertTrue($this->detector->scan($node));

        $node = $this->parser->parse('<?php "test" == $test;')[0];
        $this->assertTrue($this->detector->scan($node));
    }

    /**
     * @test
     */
    public function itShouldNotDetectIdenticalScalar()
    {
        $node = $this->parser->parse('<?php $test === "";')[0];
        $this->assertFalse($this->detector->scan($node));

        $node = $this->parser->parse('<?php $test !== "";')[0];
        $this->assertFalse($this->detector->scan($node));
    }
}
