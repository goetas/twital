<?php
namespace Goetas\Twital\SourceAdapter;

use Goetas\Twital\Template;
class XHTMLAdapter extends XMLAdapter
{

    public function load($xml)
    {
        $dom = new \DOMDocument('1.0', 'UTF-8');

        if (! @$dom->loadXML($xml)) {
            throw new \Exception("Error during XML conversion into DOM");
        }

        return new Template($dom, $this->collectMetadata($dom, $original));
    }

    public function dump(\DOMDocument $dom, $metedata)
    {
        $metedata = $template->getMetadata();
        $dom = $template->getDocument();

        if ($metedata['xmldeclaration']) {
            $source = $dom->saveXML();
        } else {
            $source = '';
            foreach ($dom->childNodes as $node) {
                $source .= $dom->saveXML($node);
            }
        }
        return $this->replaceShortTags($source);
    }

    protected function replaceShortTags($str)
    {
        $selfClosingTags = array(
            "area",
            "base",
            "br",
            "col",
            "embed",
            "hr",
            "img",
            "input",
            "keygen",
            "link",
            "menuitem",
            "meta",
            "param",
            "source",
            "track",
            "wbr"
        );
        $regex = implode("|", array_map(function ($tag) {
            return "></\s*($tag)\s*>";
        }, $selfClosingTags));
        return preg_replace("#$regex#i", "<\\1 />", $str);
    }
}