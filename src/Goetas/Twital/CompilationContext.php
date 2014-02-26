<?php
namespace Goetas\Twital;

class CompilationContext
{

    /**
     *
     * @var unknown
     */
    protected $lexer;

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

    public function __construct(\DOMDocument $doc,\Twig_Lexer $lexer, Compiler $compiler)
    {
        $this->doc = $doc;
        $this->lexer = $lexer;
        $this->compiler = compiler;
    }
    /**
     *
     * @return \Goetas\Twital\Compiler
     */
    public function getCompiler() {
    	return $this->compiler;
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
        return $this->doc->createCDATASection("__[__ {$printPart[0]}{$content}{$printPart[1]} __]__");
    }
    /**
     *
     * @param string $content
     * @return DOMCDATASection
     */
    public function crateContolNode($content)
    {
        $printPart = $this->getLexerOption('tag_block');
        return $this->doc->createCDATASection("__[__ {$printPart[0]}{$content}{$printPart[1]} __]__");
    }

    private $ref;

    private function getLexerOption($param)
    {
        if (! $this->ref) {
            $this->ref = new \ReflectionProperty(get_class($this->lexer), 'options');
            $this->ref->setAccessible(true);
        }
        $options = $this->ref->getValue($this->lexer);

        return $options[$param];
    }
}