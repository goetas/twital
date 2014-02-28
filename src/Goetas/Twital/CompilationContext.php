<?php
namespace Goetas\Twital;

class CompilationContext
{

    /**
     *
     * @var unknown
     */
    protected $lexerOptions;

    /**
     *
     * @var \DOMDocument
     */
    protected $doc;

    /**
     *
     * @var Compiler
     */
    protected $compiler;

    public function __construct(\DOMDocument $doc, TwitalLoader $compiler, array $nodes, array $attributes, array $options = array())
    {
        $this->doc = $doc;
        $this->compiler = compiler;
        $this->attributes = $attributes;
        $this->nodes = $nodes;
        $this->lexerOptions = array_merge(array(
            'tag_comment'     => array('{#', '#}'),
            'tag_block'       => array('{%', '%}'),
            'tag_variable'    => array('{{', '}}'),
            'whitespace_trim' => '-',
            'interpolation'   => array('#{', '}'),
        ), $options);
    }

    /**
     *
     * @return DOMDocument
     */
    public function getDocument()
    {
        return $this->doc;
    }

    /**
     *
     * @param string $content
     * @return DOMCDATASection
     */
    public function cratePrintNode($content)
    {
        $printPart = $this->getLexerOption('getLexerOption');
        return $this->doc->createCDATASection("__[__{$printPart[0]} {$content} {$printPart[1]}__]__");
    }

    /**
     *
     * @param string $content
     * @return DOMCDATASection
     */
    public function createControlNode($content)
    {
        $printPart = $this->getLexerOption('tag_block');
        return $this->doc->createCDATASection("__[__{$printPart[0]} " . $content . " {$printPart[1]}__]__");
    }

    private $ref;

    private function getLexerOption($name)
    {

        return $this->lexerOptions[$name];
    }

    public function compileElement(\DOMElement $node)
    {
        if (isset($this->nodes[$node->namespaceURI][$node->localName])) {
            $this->nodes[$node->namespaceURI][$node->localName]->visit($node, $this);
        } elseif (isset($this->nodes[$node->namespaceURI]['__base__'])) {
            $this->nodes[$node->namespaceURI]['__base__']->visit($node, $this);
        } else {
            if ($node->namespaceURI === TwitalLoader::NS) {
                throw new Exception("Nodo sconosciuto {$node->namespaceURI}#{$node->localName}");
            }
            if ($this->compileAttributes($node)) {
                $this->compileChilds($node);
            }
        }
    }

    public function compileAttributes(\DOMNode $node)
    {
        $continueNode = true;

        foreach (iterator_to_array($node->attributes) as $attr) {
            if (! $attr->ownerElement) {
                continue;
            } elseif (isset($this->attributes[$attr->namespaceURI][$attr->localName])) {
                $attPlugin = $this->attributes[$attr->namespaceURI][$attr->localName];
            } elseif (isset($this->attributes[$attr->namespaceURI]['__base__'])) {
                $attPlugin = $this->attributes[$attr->namespaceURI]['__base__'];
            } else {
                continue;
            }

            $return = $attPlugin->visit($attr, $this);
            if ($return !== null) {
                $continueNode = $continueNode && ($return & Attribute::STOP_NODE);
                if ($return & Attribute::STOP_ATTRIBUTE) {
                    break;
                }
            }
        }

        return $continueNode;
    }

    public function compileChilds(\DOMNode $node)
    {
        foreach (iterator_to_array($node->childNodes) as $child) {
            if ($child instanceof \DOMElement) {
                $this->compileElement($child);
            }
        }
    }
}