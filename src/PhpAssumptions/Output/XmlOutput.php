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
     * @param string  $file
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
     * @param Result $result
     */
    public function output(Result $result)
    {
        $assumptions = $result->getAssumptions();

        foreach ($assumptions as $assumption) {
            $fileElements = $this->xpath->query('/phpa/files/file[@name="' . $assumption['file'] . '"]');

            if ($fileElements->length === 0) {
                $files = $this->xpath->query('/phpa/files')->item(0);
                $fileElement = $this->document->createElement('file');
                $fileElement->setAttribute('name', $assumption['file']);
                $files->appendChild($fileElement);
            } else {
                $fileElement = $fileElements->item(0);
            }

            $lineElement = $this->document->createElement('line');
            $lineElement->setAttribute('number', $assumption['line']);
            $lineElement->setAttribute('message', $assumption['message']);
            $fileElement->appendChild($lineElement);
        }

        $this->document->documentElement->setAttribute('assumptions', $result->getAssumptionsCount());
        $this->document->documentElement->setAttribute('bool-expressions', $result->getBoolExpressionsCount());
        $this->document->documentElement->setAttribute('percentage', $result->getPercentage());

        $this->document->preserveWhiteSpace = false;
        $this->document->formatOutput = true;
        $this->document->save($this->file);

        $this->cli->out(sprintf('Written %d assumption(s) to file %s', $result->getAssumptionsCount(), $this->file));
    }
}
