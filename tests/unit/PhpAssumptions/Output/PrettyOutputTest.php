<?php

namespace unit\PhpAssumptions\Output;

use PhpAssumptions\Output\PrettyOutput;

class PrettyOutputTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var PrettyOutput
     */
    private $output;

    public function setUp()
    {
        $this->output = new PrettyOutput();
    }

    /**
     * @test
     */
    public function itShouldOutputWhatIsWritten()
    {
        $this->output->write('MyClass.php', 120, 'Weak assumption');

        ob_start();
        $this->output->flush();
        $contents = ob_get_contents();
        ob_end_clean();

        $this->assertTrue(strpos($contents, 'MyClass.php') !== false);
        $this->assertTrue(strpos($contents, '120') !== false);
        $this->assertTrue(strpos($contents, 'Weak assumption') !== false);
    }
}
