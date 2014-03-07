<?php
namespace Goetas\Twital;

class Template
{
    /**
     *
     * @var \DOMDocument
     */
    protected $document;
    /**
     *
     * @var mixed
     */
    protected $metadata;

    public function __construct(\DOMDocument $document, $metadata = null)
    {
        $this->document = $document;
        $this->metadata = $metadata;
    }
    /**
     *
     * @return \DOMDocument
     */
	public function getDocument() {
		return $this->document;
	}
    /**
     *
     * @return mixed
     */
	public function getMetadata() {
		return $this->metadata;
	}



}