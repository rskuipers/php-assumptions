<?php

namespace unit\PhpAssumptions\Output;

use League\CLImate\CLImate;
use PhpAssumptions\Cli;
use Prophecy\PhpUnit\ProphecyTestCase;
use PhpAssumptions\Output\XmlOutput;

class XmlOutputTest extends ProphecyTestCase
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
     * @var string
     */
    private $file;

    public function setUp()
    {
        $this->file = tempnam(sys_get_temp_dir(), 'xml');
        $this->cli = $this->prophesize(CLImate::class);
        $this->xmlOutput = new XmlOutput($this->cli->reveal(), $this->file);
    }

    /**
     * @test
     */
    public function itShouldGenerateValidXml()
    {
        $this->xmlOutput->write('MyClass.php', 122, 'if ($test) {');
        $this->xmlOutput->write('MyClass.php', 132, '$test ? "Yes" : "No"');
        $this->xmlOutput->write('MyOtherClass.php', 12, 'if ($test !== false) {');
        $this->xmlOutput->flush();

        $version = Cli::VERSION;

        $expected = <<<XML
<?xml version="1.0"?>
<phpa version="{$version}" warnings="3">
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
