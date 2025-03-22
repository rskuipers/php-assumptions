<?php

namespace tests;

use PhpAssumptions\Analyser;
use PhpAssumptions\Detector;
use PhpAssumptions\Parser\NodeVisitor;
use PhpParser\NodeTraverser;
use PhpParser\ParserFactory;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

class ExampleTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @var Analyser
     */
    private $analyser;

    public function setUp(): void
    {
        $nodeTraverser = new NodeTraverser();
        $this->analyser = new Analyser((new ParserFactory)->createForNewestSupportedVersion(), $nodeTraverser);
        $nodeTraverser->addVisitor(new NodeVisitor($this->analyser, new Detector()));
    }

    #[Test]
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

    #[Test]
    public function itShouldProperlyDetectBoolExpressions()
    {
        $file = fixture('Example.php');

        $result = $this->analyser->analyse([$file]);
        $this->assertEquals(12, $result->getBoolExpressionsCount());
    }
}
