<?php

namespace PhpAssumptions\Output;

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
     * @param string $file
     */
    public function __construct($file)
    {
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
        $this->document->preserveWhiteSpace = false;
        $this->document->formatOutput = true;
        $this->document->save($this->file);
    }
}
