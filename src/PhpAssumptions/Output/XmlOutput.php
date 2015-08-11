<?php

namespace PhpAssumptions\Output;

use PhpAssumptions\Cli;

class XmlOutput implements OutputInterface
{
    /**
     * @var \SimpleXMLElement
     */
    private $document;

    /**
     * @var string
     */
    private $file;

    /**
     * @param string $file
     */
    public function __construct($file)
    {
        $this->file = $file;
        $this->document = new \SimpleXMLElement('<phpa version="' . Cli::VERSION . '"><files></files></phpa>');
    }

    /**
     * @param string $file
     * @param string $line
     * @param string $message
     */
    public function write($file, $line, $message)
    {
        $fileElements = $this->document->xpath('/phpa/files/file[@name="' . $file . '"]');
        if (count($fileElements) === 0) {
            $fileElement = $this->document->xpath('/phpa/files')[0]->addChild('file');
            $fileElement->addAttribute('name', $file);
        } else {
            $fileElement = $fileElements[0];
        }

        $lineElement = $fileElement->addChild('line');
        $lineElement->addAttribute('number', $line);
        $lineElement->addAttribute('message', $message);
    }

    public function flush()
    {
        $this->document->saveXML($this->file);
    }
}
