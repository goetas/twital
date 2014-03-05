<?php
namespace Goetas\Twital\Extension;

use DOMDocument;
use Goetas\Twital\Attribute;
use Goetas\Twital\Node;
use Goetas\Twital\Twital;
use Goetas\Twital\SourceAdapter;
use Goetas\Twital\SourceAdapter\NamespaceSourceAdapter;
use Goetas\Twital\SourceAdapter\PostSourceFilterAdapter;
use Goetas\Twital\SourceAdapter\XMLAdapter;
use Goetas\Twital\SourceAdapter\XHTMLAdapter;
use Goetas\Twital\SourceAdapter\HTML5Adapter;

class CoreExtension extends AbstractExtension
{

    public function getAdapters()
    {
        return array(
            'xml' => new XMLAdapter()
        );
    }
    /*
     * public function getSourceAdapter($name, SourceAdapter $adapter, Twital $twital) { $adapter = new NamespaceSourceAdapter($adapter, $this->getCustomNamespaces()); $adapter = new PostSourceFilterAdapter($adapter, $this->getPostFilters()); return $adapter; }
     */
    public function getAttributes()
    {
        $attributes = array();
        $attributes[Twital::NS]['__base__'] = new Attribute\BaseAttribute();
        $attributes[Twital::NS]['set'] = new Attribute\SetAttribute();
        $attributes[Twital::NS]['content'] = new Attribute\ContentAttribute();
        $attributes[Twital::NS]['omit'] = new Attribute\OmitAttribute();
        $attributes[Twital::NS]['capture'] = new Attribute\CaptureAttribute();
        $attributes[Twital::NS]['attr'] = new Attribute\AttrAttribute();
        $attributes[Twital::NS]['attr-append'] = new Attribute\AttrAppendAttribute();
        return $attributes;
    }

    public function getNodes()
    {
        $nodes = array();
        $nodes[Twital::NS]['extends'] = new Node\ExtendsNode();
        $nodes[Twital::NS]['block'] = new Node\BlockNode();
        $nodes[Twital::NS]['macro'] = new Node\MacroNode();
        $nodes[Twital::NS]['import'] = new Node\ImportNode();
        $nodes[Twital::NS]['include'] = new Node\IncludeNode();
        $nodes[Twital::NS]['omit'] = new Node\OmitNode();
        $nodes[Twital::NS]['embed'] = new Node\EmbedNode();
        return $nodes;
    }

    protected function getCustomNamespaces()
    {
        return array(
            't' => Twital::NS
        );
    }

    protected function getPostFilters()
    {
        return array(
            function ($string)
            {
                return preg_replace('#<(.*) xmlns:[a-zA-Z0-9]+=("|\')' . Twital::NS . '("|\')(.*)>#m', "<\\1\\4>", $string);
            },
            function ($string)
            {
                return str_replace(array(
                    "<![CDATA[__[__",
                    "__]__]]>"
                ), "", $string);
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
