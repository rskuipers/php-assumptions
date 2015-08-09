<?php

namespace integration\PhpAssumptions;

use PhpAssumptions\Cli;
use Prophecy\PhpUnit\ProphecyTestCase;

class CliTest extends ProphecyTestCase
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
    public function itShouldDetectWeakAssumption()
    {
        ob_start();
        $this->cli->handle([fixture('MyClass.php')]);
        $contents = ob_get_contents();
        ob_end_clean();

        $this->assertContains('MyClass.php:9', $contents);
    }
}
