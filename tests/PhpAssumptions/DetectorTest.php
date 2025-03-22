<?php

namespace tests\PhpAssumptions;

use PhpAssumptions\Detector;
use PhpParser\Parser;
use PhpParser\ParserFactory;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

class DetectorTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @var Parser
     */
    private $parser;

    /**
     * @var Detector
     */
    private $detector;

    public function setUp(): void
    {
        $this->parser = (new ParserFactory)->createForNewestSupportedVersion();
        $this->detector = new Detector();
    }

    #[Test]
    public function itShouldDetectNotNull()
    {
        $node = $this->parser->parse('<?php $var !== null;')[0];
        $this->assertTrue($this->detector->scan($node));

        $node = $this->parser->parse('<?php null !== $var;')[0];
        $this->assertTrue($this->detector->scan($node));
    }

    #[Test]
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

    #[Test]
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

    #[Test]
    public function itShouldDetectEqualsScalar()
    {
        $node = $this->parser->parse('<?php $test == "test";')[0];
        $this->assertTrue($this->detector->scan($node));

        $node = $this->parser->parse('<?php "test" == $test;')[0];
        $this->assertTrue($this->detector->scan($node));
    }

    #[Test]
    public function itShouldDetectWhileAssumptions()
    {
        $node = $this->parser->parse('<?php while ($test);')[0];
        $this->assertTrue($this->detector->scan($node));
    }

    #[Test]
    public function itShouldDetectForAssumptions()
    {
        $node = $this->parser->parse('<?php for ($i = 0; $i; $i++);')[0];
        $this->assertTrue($this->detector->scan($node));
    }
}
