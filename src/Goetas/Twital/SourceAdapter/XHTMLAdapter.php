<?php
namespace Goetas\Twital\Dumper;

class XHTMLAdapter extends XMLAdapter
{

    public function load($xml)
    {
        $dom = new \DOMDocument('1.0', 'UTF-8');

        if (! @$dom->loadXML($xml)) {
            throw new \Exception("Error during XML conversion into DOM");
        }
        return $dom;
    }

    public function dump(\DOMDocument $dom, $metadata)
    {
        $string = parent::dump($dom, $metadata);
        $string = $this->replaceShortTags($string);

        return $string;
    }

    protected function replaceShortTags($str)
    {
        $str = preg_replace_callback(
            "#<(title|iframe|textarea|div|span|p|h1|h2|h3|h4|h5|h6|label|fieldset|legend|strong|small|cite|script|style|select|em|td|b)/>#i",
            function ($mch)
            {
                if (strlen(trim($mch[2]))) {
                    return "<$mch[1] " . trim($mch[2]) . "></$mch[1]>";
                } else {
                    return "<$mch[1]></$mch[1]>";
                }
            }, $str);
        return $str;
    }
}