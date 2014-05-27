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
    /**
     * {@inheritdoc}
     */
    public function dump(Template $template)
    {
        $metadata = $template->getMetadata();
        $dom = $template->getDocument();
        $dom->preserveWhiteSpace = true;
        $dom->formatOutput = false;

        if ($metadata['xmldeclaration']) {
            $xml = $dom->saveXML();
        } else {
            $xml = '';
            foreach ($dom->childNodes as $node) {
                $xml .= $dom->saveXML($node, LIBXML_NOEMPTYTAG);
                if ($node instanceof \DOMDocumentType) {
                    $xml.= PHP_EOL;
                }
            }
        }

        return $this->replaceShortTags($xml);
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

        return preg_replace("#$regex#i", "/>", $str);
    }
}
