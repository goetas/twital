<?php
namespace Goetas\Twital\Extension;

use DOMDocument;
use Goetas\Twital\DOMLoader\XMLDOMLoader;
use Goetas\Twital\DOMLoader\XHTMLDOMLoader;
use Goetas\Twital\Extension;
use Goetas\Twital\Attribute;
use Goetas\Twital\Node;

class CoreExtension implements Extension
{

    const NS = "Twital";

    public function getDOMLoaders()
    {
        return array(
            'xml' => new XMLDOMLoader()
        );
    }

    public function getAttributes()
    {
        $attributes = array();
        $attributes[self::NS]['__base__'] = new Attribute\BaseAttribute();
        $attributes[self::NS]['set'] = new Attribute\SetAttribute();
        $attributes[self::NS]['content'] = new Attribute\ContentAttribute();
        $attributes[self::NS]['omit'] = new Attribute\OmitAttribute();
        $attributes[self::NS]['capture'] = new Attribute\CaptureAttribute();
        $attributes[self::NS]['attr'] = new Attribute\AttrAttribute();
        $attributes[self::NS]['attr-append'] = new Attribute\AttrAppendAttribute();
        $attributes[self::NS]['attr-translate'] = new Attribute\AttrTranslateAttribute();
        return $attributes;
    }

    public function getNodes()
    {
        $nodes = array();
        $nodes[self::NS]['extends'] = new Node\ExtendsNode();
        $nodes[self::NS]['block'] = new Node\BlockNode();
        $nodes[self::NS]['macro'] = new Node\MacroNode();
        $nodes[self::NS]['import'] = new Node\ImportNode();
        $nodes[self::NS]['include'] = new Node\IncludeNode();
        return $nodes;
    }

    public function getPostFilters()
    {
        return array(
            function ($string)
            {
                return preg_replace('#<(.*) xmlns:[a-zA-Z0-9]+=("|\')' . CoreExtension::NS . '("|\')(.*)>#m', "<\\1\\4>", $string);
            },
            function ($string)
            {
                return preg_replace_callback('/ __attr__="(__a[0-9a-f]+)"/', function ($mch)
                {
                    return '{% for ___ak,____av in ' . $mch[1] . ' %} {{____ak}}="{{ ____av|join(\'\') }}"{% endfor %}';
                }, $string);
            }
        );
    }

    public function getPreFilters()
    {
        return array();
    }
}
