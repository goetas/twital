<?php
namespace Goetas\Twital\Extension;

use DOMDocument;
use Goetas\Twital\Attribute;
use Goetas\Twital\Node;
use Goetas\Twital\TwitalLoader;
use Goetas\Twital\SourceAdapter\XMLAdapter;

class CoreExtension extends AbstractExtension
{
    public function getPrefixes()
    {
        return array(
            't' => TwitalLoader::NS
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
        $attributes[TwitalLoader::NS]['__base__'] = new Attribute\BaseAttribute();
        $attributes[TwitalLoader::NS]['set'] = new Attribute\SetAttribute();
        $attributes[TwitalLoader::NS]['content'] = new Attribute\ContentAttribute();
        $attributes[TwitalLoader::NS]['omit'] = new Attribute\OmitAttribute();
        $attributes[TwitalLoader::NS]['capture'] = new Attribute\CaptureAttribute();
        $attributes[TwitalLoader::NS]['attr'] = new Attribute\AttrAttribute();
        $attributes[TwitalLoader::NS]['attr-append'] = new Attribute\AttrAppendAttribute();
        return $attributes;
    }

    public function getNodes()
    {
        $nodes = array();
        $nodes[TwitalLoader::NS]['extends'] = new Node\ExtendsNode();
        $nodes[TwitalLoader::NS]['block'] = new Node\BlockNode();
        $nodes[TwitalLoader::NS]['macro'] = new Node\MacroNode();
        $nodes[TwitalLoader::NS]['import'] = new Node\ImportNode();
        $nodes[TwitalLoader::NS]['include'] = new Node\IncludeNode();
        $nodes[TwitalLoader::NS]['omit'] = new Node\OmitNode();
        //$nodes[TwitalLoader::NS]['embed'] = new Node\IncludeNode();
        return $nodes;
    }

    public function getPostFilters()
    {
        return array(
            function ($string)
            {
                return preg_replace('#<(.*) xmlns:[a-zA-Z0-9]+=("|\')' . TwitalLoader::NS . '("|\')(.*)>#m', "<\\1\\4>", $string);
            },
            function ($string)
            {
                return str_replace(array("<![CDATA[__[__", "__]__]]>"), "", $string);
            },
            function ($string)
            {
                return preg_replace_callback('/ __attr__="(__a[0-9a-f]+)"/', function ($mch)
                {
                    return '{% for ____ak,____av in ' . $mch[1] . ' if ____av|length>0 %} {{____ak | raw}}="{{ ____av|join(\'\') }}"{% endfor %}';
                }, $string);
            }
        );
    }
}
