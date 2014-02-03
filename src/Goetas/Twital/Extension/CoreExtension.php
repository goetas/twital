<?php
namespace Goetas\Twital\Extension;

use DOMDocument;
use Goetas\Twital\DOMLoader\XMLDOMLoader;
use Goetas\Twital\DOMLoader\XHTMLDOMLoader;
use Goetas\Twital\Extension;
use Goetas\Twital\Attribute;
use Goetas\Twital\Node;
use Goetas\Twital\Compiler;

class CoreExtension implements Extension
{


    public function getDOMLoaders()
    {
        return array(
            'xml' => new XMLDOMLoader()
        );
    }

    public function getAttributes()
    {
        $attributes = array();
        $attributes[Compiler::NS]['__base__'] = new Attribute\BaseAttribute();
        $attributes[Compiler::NS]['set'] = new Attribute\SetAttribute();
        $attributes[Compiler::NS]['content'] = new Attribute\ContentAttribute();
        $attributes[Compiler::NS]['omit'] = new Attribute\OmitAttribute();
        $attributes[Compiler::NS]['capture'] = new Attribute\CaptureAttribute();
        $attributes[Compiler::NS]['attr'] = new Attribute\AttrAttribute();
        $attributes[Compiler::NS]['attr-append'] = new Attribute\AttrAppendAttribute();
        $attributes[Compiler::NS]['attr-translate'] = new Attribute\AttrTranslateAttribute();
        return $attributes;
    }

    public function getNodes()
    {
        $nodes = array();
        $nodes[Compiler::NS]['extends'] = new Node\ExtendsNode();
        $nodes[Compiler::NS]['block'] = new Node\BlockNode();
        $nodes[Compiler::NS]['block-call'] = new Node\BlockCallNode();
        $nodes[Compiler::NS]['macro'] = new Node\MacroNode();
        $nodes[Compiler::NS]['import'] = new Node\ImportNode();
        $nodes[Compiler::NS]['include'] = new Node\IncludeNode();
        return $nodes;
    }

    public function getPostFilters()
    {
        return array(
            function ($string)
            {
                return preg_replace('#<(.*) xmlns:[a-zA-Z0-9]+=("|\')' . Compiler::NS . '("|\')(.*)>#m', "<\\1\\4>", $string);
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
