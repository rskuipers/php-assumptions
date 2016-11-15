<?php

namespace tests\PhpAssumptions;

use PhpAssumptions\Detector;
use PhpParser\ParserFactory;

class NodeVisitorTest extends \PHPUnit_Framework_TestCase
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
        $this->parser = (new ParserFactory)->create(ParserFactory::PREFER_PHP7);
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

        $node = $this->parser->parse('<?php if ($test instanceof Test) { } elseif ($test) { }')[0]->elseifs[0];
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
    public function itShouldDetectWhileAssumptions()
    {
        $node = $this->parser->parse('<?php while ($test);')[0];
        $this->assertTrue($this->detector->scan($node));
    }

    /**
     * @test
     */
    public function itShouldDetectForAssumptions()
    {
        $node = $this->parser->parse('<?php for ($i = 0; $i; $i++);')[0];
        $this->assertTrue($this->detector->scan($node));
    }
}
