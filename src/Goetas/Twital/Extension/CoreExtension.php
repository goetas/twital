<?php
namespace Goetas\Twital\Extension;

use DOMDocument;
use Goetas\Twital\Attribute;
use Goetas\Twital\Node;
use Goetas\Twital\Compiler;
use Goetas\Twital\Loader\XMLLoader;
use Goetas\Twital\Dumper\XMLDumper;

class CoreExtension extends AbstractExtension
{
    public function getPrefixes()
    {
        return array(
            't' => Compiler::NS
        );
    }

    public function getLoaders()
    {
        return array(
            'xml' => new XMLLoader()
        );
    }
    public function getDumpers()
    {
        return array(
            'xml' => new XMLDumper()
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
        return $attributes;
    }

    public function getNodes()
    {
        $nodes = array();
        $nodes[Compiler::NS]['extends'] = new Node\ExtendsNode();
        $nodes[Compiler::NS]['block'] = new Node\BlockNode();
        $nodes[Compiler::NS]['macro'] = new Node\MacroNode();
        $nodes[Compiler::NS]['import'] = new Node\ImportNode();
        $nodes[Compiler::NS]['include'] = new Node\IncludeNode();
        $nodes[Compiler::NS]['omit'] = new Node\OmitNode();
        //$nodes[Compiler::NS]['embed'] = new Node\IncludeNode();
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
