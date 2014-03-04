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
    protected $document;
    /**
     *
     * @var Compiler
     */
    protected $compiler;



    public function __construct(\DOMDocument $document, Compiler $compiler, array $lexerOptions = array())
    {
        $this->document = $document;
        $this->compiler = $compiler;

        $this->lexerOptions = array_merge(array(
            'tag_comment'     => array('{#', '#}'),
            'tag_block'       => array('{%', '%}'),
            'tag_variable'    => array('{{', '}}'),
            'whitespace_trim' => '-',
            'interpolation'   => array('#{', '}'),
        ), $lexerOptions);
    }

    /**
     *
     * @return DOMDocument
     */
    public function getDocument()
    {
        return $this->document;
    }

    /**
     *
     * @param string $content
     * @return DOMCDATASection
     */
    public function createPrintNode($content)
    {
        $printPart = $this->getLexerOption('tag_variable');
        return $this->document->createCDATASection("__[__{$printPart[0]} {$content} {$printPart[1]}__]__");
    }

    /**
     *
     * @param string $content
     * @return DOMCDATASection
     */
    public function createControlNode($content)
    {
        $printPart = $this->getLexerOption('tag_block');
        return $this->document->createCDATASection("__[__{$printPart[0]} " . $content . " {$printPart[1]}__]__");
    }

    private $ref;

    private function getLexerOption($name)
    {

        return $this->lexerOptions[$name];
    }

    public function compileElement(\DOMElement $node)
    {
        $nodes = $this->compiler->getNodes();
        if (isset($nodes[$node->namespaceURI][$node->localName])) {
            $nodes[$node->namespaceURI][$node->localName]->visit($node, $this);
        } elseif (isset($nodes[$node->namespaceURI]['__base__'])) {
            $nodes[$node->namespaceURI]['__base__']->visit($node, $this);
        } else {
            if ($node->namespaceURI === Twital::NS) {
                throw new Exception("Nodo sconosciuto {$node->namespaceURI}#{$node->localName}");
            }
            if ($this->compileAttributes($node)) {
                $this->compileChilds($node);
            }
        }
    }

    public function compileAttributes(\DOMNode $node)
    {
        $node->attributes = $this->compiler->getAttributes();
        $continueNode = true;
        foreach (iterator_to_array($node->attributes) as $attr) {
            if (! $attr->ownerElement) {
                continue;
            } elseif (isset($attributes[$attr->namespaceURI][$attr->localName])) {
                $attPlugin = $attributes[$attr->namespaceURI][$attr->localName];
            } elseif (isset($attributes[$attr->namespaceURI]['__base__'])) {
                $attPlugin = $attributes[$attr->namespaceURI]['__base__'];
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

	public function getCompiler() {
	return $this->compiler;
}

	public function setCompiler(Compiler $compiler) {
	$this->compiler = $compiler;
	return $this;
}

}