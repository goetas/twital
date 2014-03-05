<?php
namespace Goetas\Twital\SourceAdapter;

use Goetas\Twital\SourceAdapter;
use Goetas\Twital\Template;

class XMLAdapter implements SourceAdapter
{

    public function load($xml)
    {
        $dom = new \DOMDocument('1.0', 'UTF-8');

        if (! @$dom->loadXML($xml)) {
            throw new \Exception("Error during XML conversion into DOM");
        }

        return new Template($dom, $this->collectMetadata($dom, $original));
    }
    protected function collectMetadata(\DOMDocument $dom, $original)
    {
        $metedata = array();

        $metedata['xmldeclaration'] = strpos(rtrim($original), '<?xml ') === 0;
        $metedata['doctype'] = ! ! $dom->doctype;

        return $metedata;
    }
    public function dump(Template $template)
    {
        $metedata = $template->getMetadata();
        $dom = $template->getTemplate();

        if ($metedata['xmldeclaration']) {
            return $dom->saveXML();
        } else {
            $source = '';
            foreach ($dom->childNodes as $node) {
                $source .= $dom->saveXML($node);
            }
            return $source;
        }
    }
}