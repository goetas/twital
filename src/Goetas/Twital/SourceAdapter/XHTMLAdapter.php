<?php
namespace Goetas\Twital\SourceAdapter;

use Goetas\Twital\Template;
/**
 *
 * @author Asmir Mustafic <goetas@gmail.com>
 *
 */
class XHTMLAdapter extends XMLAdapter
{

    public function load($source)
    {
        $dom = new \DOMDocument('1.0', 'UTF-8');

        if (! @$dom->loadXML($source)) {
            throw new \Exception("Error during XML conversion into DOM");
        }

        return new Template($dom, $this->collectMetadata($dom, $source));
    }

    public function dump(Template $template)
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