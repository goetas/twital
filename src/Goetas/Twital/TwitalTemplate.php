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
    protected $template;
    /**
     *
     * @var mixed
     */
    protected $metadata;

    public function __construct(\DOMDocument $template, $metadata = null)
    {
        $this->template = $template;
        $this->metadata = $metadata;
    }
    /**
     *
     * @return \DOMDocument
     */
	public function getTemplate() {
		return $this->template;
	}
    /**
     *
     * @return mixed
     */
	public function getMetadata() {
		return $this->metadata;
	}



}