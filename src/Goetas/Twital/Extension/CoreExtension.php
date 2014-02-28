<?php
namespace Goetas\Twital\Extension;

use DOMDocument;
use Goetas\Twital\Attribute;
use Goetas\Twital\Node;
use Goetas\Twital\TwitalEnviroment;
use Goetas\Twital\SourceAdapter\XMLAdapter;

class CoreExtension extends AbstractExtension
{
    public function getPrefixes()
    {
        return array(
            't' => TwitalEnviroment::NS
        );
    }

    public function getSourceAdapters()
    {
        return array(
            'xml' => new XMLAdapter()
        );
    }

    public function getAttributes()
    {
        $attributes = array();
        $attributes[TwitalEnviroment::NS]['__base__'] = new Attribute\BaseAttribute();
        $attributes[TwitalEnviroment::NS]['set'] = new Attribute\SetAttribute();
        $attributes[TwitalEnviroment::NS]['content'] = new Attribute\ContentAttribute();
        $attributes[TwitalEnviroment::NS]['omit'] = new Attribute\OmitAttribute();
        $attributes[TwitalEnviroment::NS]['capture'] = new Attribute\CaptureAttribute();
        $attributes[TwitalEnviroment::NS]['attr'] = new Attribute\AttrAttribute();
        $attributes[TwitalEnviroment::NS]['attr-append'] = new Attribute\AttrAppendAttribute();
        return $attributes;
    }

    public function getNodes()
    {
        $nodes = array();
        $nodes[TwitalEnviroment::NS]['extends'] = new Node\ExtendsNode();
        $nodes[TwitalEnviroment::NS]['block'] = new Node\BlockNode();
        $nodes[TwitalEnviroment::NS]['macro'] = new Node\MacroNode();
        $nodes[TwitalEnviroment::NS]['import'] = new Node\ImportNode();
        $nodes[TwitalEnviroment::NS]['include'] = new Node\IncludeNode();
        $nodes[TwitalEnviroment::NS]['omit'] = new Node\OmitNode();
        //$nodes[TwitalEnviroment::NS]['embed'] = new Node\IncludeNode();
        return $nodes;
    }

    public function getPostFilters()
    {
        return array(
            function ($string)
            {
                return preg_replace('#<(.*) xmlns:[a-zA-Z0-9]+=("|\')' . TwitalEnviroment::NS . '("|\')(.*)>#m', "<\\1\\4>", $string);
            },
            function ($string)
            {
                return str_replace(array("<![CDATA[__[__", "__]__]]>"), "", $string);
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
}
