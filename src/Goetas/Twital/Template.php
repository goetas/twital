<?php
namespace Goetas\Twital;

/**
 * This class represents a template.
 * A valid template is a {DOMDocument} with some additional metadata.
 *
 * @author Asmir Mustafic <goetas@gmail.com>
 *
 */
class Template
{
    /**
     * The template {DOMDocument}
     *
     * @var \DOMDocument
     */
    private $document;

    /**
     * Template metadatas
     *
     * @var mixed
     */
    private $metadata;

    /**
     * @param \DOMDocument $document The template {DOMDocument}
     * @param mixed $metadata Template metadatas
     */
    public function __construct(\DOMDocument $document, $metadata = null)
    {
        $this->document = $document;
        $this->metadata = $metadata;
    }

    /**
     * Returns the {DOMDocument} of a template
     *
     * @return \DOMDocument
     */
    public function getDocument()
    {
        return $this->document;
    }

    /**
     * Return template metadatas.
     *
     * @return mixed
     */
    public function getMetadata()
    {
        return $this->metadata;
    }
}
