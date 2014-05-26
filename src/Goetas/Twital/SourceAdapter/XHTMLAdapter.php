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
        return $this->replaceShortTags(parent::dump($template));
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
