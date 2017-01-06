<?php
namespace Goetas\Twital\SourceAdapter;

use Masterminds\HTML5;
use Goetas\Twital\SourceAdapter;
use Goetas\Twital\Template;
use Goetas\Twital\Helper\DOMHelper;

/**
 *
 * @author Asmir Mustafic <goetas@gmail.com>
 *
 */
class HTML5Adapter implements SourceAdapter
{
    private $html5;

    private function getHtml5()
    {
        if (!$this->html5) {
            $this->html5 = new HTML5(array(
                "xmlNamespaces" => true
            ));
        }
        return $this->html5;
    }

    /**
     * {@inheritdoc}
     */
    public function load($source)
    {
        $html5 = $this->getHtml5();
        $f = $html5->loadHTMLFragment($source);
        $dom = new \DOMDocument('1.0', 'UTF-8');
        $dom->appendChild($dom->importNode($f, true));

        return new Template($dom, $this->collectMetadata($dom, $source));
    }

    /**
     * {@inheritdoc}
     */
    public function dump(Template $template)
    {
        $metadata = $template->getMetadata();
        $dom = $template->getDocument();
        $html5 = $this->getHtml5();
        return $html5->saveHTML($metadata['fragment'] ? $dom->childNodes : $dom);
    }

    /**
     * Collect some metadata about $dom and $content
     * @param \DOMDocument $dom
     * @param string $source
     * @return mixed
     */
    protected function collectMetadata(\DOMDocument $dom, $source)
    {
        $metadata = array();

        $metadata['doctype'] = !!$dom->doctype;
        $metadata['fragment'] = strpos(rtrim($source), '<!DOCTYPE html>') !== 0;

        return $metadata;
    }
}
