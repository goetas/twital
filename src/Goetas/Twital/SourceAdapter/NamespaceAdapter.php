<?php
namespace Goetas\Twital\SourceAdapter;

use Goetas\Twital\SourceAdapter;
use Goetas\Twital\NamespaceAdapter;
use Goetas\Twital\NamespaceHelper;

class NamespaceSourceAdapter extends AbstractWrapAdapter
{

    /**
     *
     * @var SourceAdapter
     */
    protected $customNamespaces;

    public function __construct(SourceAdapter $adapter, array $customNamespaces)
    {
        parent::__construct($adapter);
        $this->customNamespaces = $customNamespaces;
    }

    public function load($xml)
    {
        $template = parent::load($xml);
        $this->checkDocumentNamespaces($template->getTemplate());
        return $template;
    }

    protected function checkDocumentNamespaces(\DOMDocument $dom)
    {
        if ($this->customNamespaces) {
            foreach (iterator_to_array($dom->childNodes) as $child) {
                if ($child instanceof \DOMElement) {
                    NamespaceHelper::checkNamespaces($child, $this->customNamespaces);
                }
            }
        }
    }
}