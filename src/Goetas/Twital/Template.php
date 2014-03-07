<?php
namespace Goetas\Twital;

use Goetas\Twital\Extension\CoreExtension;
use Goetas\Twital\Extension\I18nExtension;
use Goetas\Twital\Extension\HTML5Extension;

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