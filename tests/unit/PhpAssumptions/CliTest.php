<?php

namespace unit\PhpAssumptions;

use PhpAssumptions\Cli;

class CliTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Cli
     */
    private $cli;

    public function setUp()
    {
        $this->cli = new Cli();
    }

    /**
     * @test
     */
    public function itShouldShowHelpWithNoArgs()
    {
        ob_start();
        $this->cli->handle([]);
        $contents = ob_get_contents();
        ob_end_clean();

        $this->assertTrue(strpos($contents, 'Usage: phpa') !== false);
    }

    /**
     * @test
     */
    public function itShouldTraverseADirectoryIfTargetIsADirectory()
    {
        ob_start();
        $this->cli->handle([FIXTURES_DIR]);
        $contents = ob_get_contents();
        ob_end_clean();

        // It should traverse the fixtures directory and hit MyClass as violation
        $this->assertTrue(strpos($contents, 'fixtures/MyClass.php:9: $dog !== null;') !== false);
    }
}
