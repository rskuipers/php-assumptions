<?php

namespace tests;

use PhpAssumptions\Analyser;
use PhpAssumptions\Detector;
use PhpAssumptions\Parser\NodeVisitor;
use PhpParser\NodeTraverser;
use PhpParser\ParserFactory;

class ExampleTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Analyser
     */
    private $analyser;

    public function setUp()
    {
        $nodeTraverser = new NodeTraverser();
        $this->analyser = new Analyser((new ParserFactory)->create(ParserFactory::PREFER_PHP7), $nodeTraverser);
        $nodeTraverser->addVisitor(new NodeVisitor($this->analyser, new Detector()));
    }

    /**
     * @test
     */
    public function itShouldProperlyDetectAssumptions()
    {
        $file = fixture('Example.php');

        $result = $this->analyser->analyse([$file]);

        $this->assertSame([
            [
                'file' => $file,
                'line' => 9,
                'message' => '$test = $bla && $bla === \'test\' || $bla === \'ha\' ? \'haha\' : \'test\';',
            ],
            [
                'file' => $file,
                'line' => 10,
                'message' => 'if ($test && $test > 0) {',
            ],
            [
                'file' => $file,
                'line' => 12,
                'message' => '} elseif (!$test) {',
            ],
            [
                'file' => $file,
                'line' => 19,
                'message' => 'while ($test) {',
            ],
            [
                'file' => $file,
                'line' => 23,
                'message' => 'for ($i = 0; $i; $i++) {',
            ],
        ], $result->getAssumptions());
    }

    /**
     * @test
     */
    public function itShouldProperlyDetectBoolExpressions()
    {
        $file = fixture('Example.php');

        $result = $this->analyser->analyse([$file]);
        $this->assertEquals(12, $result->getBoolExpressionsCount());
    }
}
