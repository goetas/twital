<?php
namespace Goetas\Twital;

class CompilationContext
{

    /**
     *
     * @var array
     */
    protected $lexerOptions;

    /**
     *
     * @var Template
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

    private function getLexerOption($name)
    {

        return $this->lexerOptions[$name];
    }

	public function getCompiler() {
    	return $this->compiler;
    }

	public function setCompiler(Compiler $compiler) {
    	$this->compiler = $compiler;
    	return $this;
    }

}