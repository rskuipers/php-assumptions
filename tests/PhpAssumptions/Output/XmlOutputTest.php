<?php

namespace tests\PhpAssumptions\Output;

use League\CLImate\CLImate;
use PhpAssumptions\Cli;
use PhpAssumptions\Output\Result;
use PhpAssumptions\Output\XmlOutput;

class XmlOutputTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var XmlOutput
     */
    private $xmlOutput;

    /**
     * @var CLImate
     */
    private $cli;

    /**
     * @var Result
     */
    private $result;

    /**
     * @var string
     */
    private $file;

    public function setUp()
    {
        $this->file = tempnam(sys_get_temp_dir(), 'xml');
        $this->cli = $this->prophesize(CLImate::class);
        $this->result = $this->prophesize(Result::class);
        $this->xmlOutput = new XmlOutput($this->cli->reveal(), $this->file);
    }

    /**
     * @test
     */
    public function itShouldGenerateValidXml()
    {
        $this->result->getAssumptions()->shouldBeCalled()->willReturn([
            [
                'file' => 'MyClass.php',
                'line' => 122,
                'message' => 'if ($test) {'
            ],
            [
                'file' => 'MyClass.php',
                'line' => 132,
                'message' => '$test ? "Yes" : "No"'
            ],
            [
                'file' => 'MyOtherClass.php',
                'line' => 12,
                'message' => 'if ($test !== false) {'
            ]
        ]);

        $this->result->getAssumptionsCount()->shouldBeCalled()->willReturn(3);
        $this->result->getPercentage()->shouldBeCalled()->willReturn(60);
        $this->result->getBoolExpressionsCount()->shouldBeCalled()->willReturn(5);

        $this->xmlOutput->output($this->result->reveal());

        $version = Cli::VERSION;

        $expected = <<<XML
<?xml version="1.0"?>
<phpa version="{$version}" assumptions="3" bool-expressions="5" percentage="60">
    <files>
        <file name="MyClass.php">
            <line number="122" message="if (\$test) {" />
            <line number="132" message="\$test ? &quot;Yes&quot; : &quot;No&quot;" />
        </file>
        <file name="MyOtherClass.php">
            <line number="12" message="if (\$test !== false) {" />
        </file>
    </files>
</phpa>
XML;

        $this->assertXmlStringEqualsXmlString($expected, file_get_contents($this->file));
    }
}
