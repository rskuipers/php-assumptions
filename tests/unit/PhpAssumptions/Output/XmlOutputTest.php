<?php

namespace unit\PhpAssumptions\Output;

use PhpAssumptions\Cli;
use Prophecy\PhpUnit\ProphecyTestCase;
use PhpAssumptions\Output\XmlOutput;

class XmlOutputTest extends ProphecyTestCase
{
    private $cloverOutput;

    public function setUp()
    {
        $this->cloverOutput = new XmlOutput('php://output');
    }

    /**
     * @test
     */
    public function itShouldGenerateValidCloverXml()
    {
        $this->cloverOutput->write('MyClass.php', 122, 'if ($test) {');
        $this->cloverOutput->write('MyClass.php', 132, '$test ? "Yes" : "No"');
        $this->cloverOutput->write('MyOtherClass.php', 12, 'if ($test !== false) {');

        ob_start();
        $this->cloverOutput->flush();
        $xml = ob_get_contents();
        ob_end_clean();

        $version = Cli::VERSION;

        $expected = <<<XML
<?xml version="1.0"?>
<phpa version="{$version}">
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

        $this->assertXmlStringEqualsXmlString($expected, $xml);
    }
}
