<?php

namespace PhpAssumptions\Output;

use League\CLImate\CLImate;
use PhpAssumptions\Cli;

class XmlOutput implements OutputInterface
{
    /**
     * @var \DOMDocument
     */
    private $document;

    /**
     * @var string
     */
    private $file;

    /**
     * @var \DOMXPath
     */
    private $xpath;

    /**
     * @var CLImate
     */
    private $cli;

    /**
     * @param CLImate $cli
     * @param string $file
     */
    public function __construct(CLImate $cli, $file)
    {
        $this->cli = $cli;
        $this->file = $file;
        $this->document = new \DOMDocument();

        $phpaNode = $this->document->createElement('phpa');
        $phpaNode->setAttribute('version', Cli::VERSION);
        $this->document->appendChild($phpaNode);

        $filesNode = $this->document->createElement('files');
        $phpaNode->appendChild($filesNode);

        $this->xpath = new \DOMXPath($this->document);
    }

    /**
     * @param string $file
     * @param string $line
     * @param string $message
     */
    public function write($file, $line, $message)
    {
        $fileElements = $this->xpath->query('/phpa/files/file[@name="' . $file . '"]');

        if ($fileElements->length === 0) {
            $files = $this->xpath->query('/phpa/files')->item(0);
            $fileElement = $this->document->createElement('file');
            $fileElement->setAttribute('name', $file);
            $files->appendChild($fileElement);
        } else {
            $fileElement = $fileElements->item(0);
        }

        $lineElement = $this->document->createElement('line');
        $lineElement->setAttribute('number', $line);
        $lineElement->setAttribute('message', $message);
        $fileElement->appendChild($lineElement);
    }

    public function flush()
    {
        $totalWarnings = $this->xpath->query('/phpa/files/file/line')->length;
        $this->document->documentElement->setAttribute('warnings', $totalWarnings);

        $this->document->preserveWhiteSpace = false;
        $this->document->formatOutput = true;
        $this->document->save($this->file);

        $this->cli->out(sprintf('Written %d warning(s) to file %s', $totalWarnings, $this->file));
    }
}
