<?php
namespace Goetas\Twital;

class TwitalEnviroment extends \Twig_Environment
{

    private $twig;

    private $twitalCompiler;

    private $twitalPathComponent;

    public function __construct(\Twig_Environment $twig = null, Compiler $twitalCompiler = null, $twitalPathComponent = '.twital')
    {
        $this->twig = $twig;
        $this->twitalCompiler = $twitalCompiler;
        $this->twitalPathComponent = $twitalPathComponent;
    }

    public function compileSource($source, $name = null)
    {
        if (strpos($name, $this->twitalPathComponent) !== false) {
            $source = $this->twitalCompiler->compile($source);
        }
        return $this->twig->compileSource($source, $name);
    }

    public function loadTemplate($name, $index = null)
    {
        $cls = $this->getTemplateClass($name, $index);

        if (isset($this->loadedTemplates[$cls])) {
            return $this->loadedTemplates[$cls];
        }

        if (! class_exists($cls, false)) {
            if (false === $cache = $this->getCacheFilename($name)) {
                eval('?>' . $this->compileSource($this->getLoader()->getSource($name), $name));
            } else {
                if (! is_file($cache) || ($this->isAutoReload() && ! $this->isTemplateFresh($name, filemtime($cache)))) {
                    $this->writeCacheFile($cache, $this->compileSource($this->getLoader()->getSource($name), $name));
                }
                require_once $cache;
            }
        }

        if (! $this->runtimeInitialized) {
            $this->initRuntime();
            $this->runtimeInitialized = true;
        }

        return $this->loadedTemplates[$cls] = new $cls($this);
    }

    public function getBaseTemplateClass()
    {
        return $this->twig->getBaseTemplateClass();
    }

    public function setBaseTemplateClass($class)
    {
        $this->twig->setBaseTemplateClass($class);
    }

    public function enableDebug()
    {
        $this->twig->enableDebug();
    }

    public function disableDebug()
    {
        $this->twig->disableDebug();
    }

    public function isDebug()
    {
        return $this->twig->isDebug();
    }

    public function enableAutoReload()
    {
        $this->twig->enableAutoReload();
    }

    public function disableAutoReload()
    {
        $this->twig->disableAutoReload();
    }

    public function isAutoReload()
    {
        return $this->twig->isAutoReload();
    }

    public function enableStrictVariables()
    {
        $this->twig->enableStrictVariables();
    }

    public function disableStrictVariables()
    {
        $this->twig->disableStrictVariables();
    }

    public function isStrictVariables()
    {
        return $this->twig->isStrictVariables();
    }

    public function getCache()
    {
        return $this->twig->getCache();
    }

    public function setCache($cache)
    {
        $this->twig->setCache($cache);
    }

    public function getCacheFilename($name)
    {
        return $this->twig->getCacheFilename($name);
    }

    public function getTemplateClass($name, $index = null)
    {
        return $this->twig->getTemplateClass($name, $index);
    }

    public function getTemplateClassPrefix()
    {
        return $this->twig->getTemplateClassPrefix();
    }

    public function render($name, array $context = array())
    {
        return $this->twig->render($name, $context);
    }

    public function display($name, array $context = array())
    {
        $this->twig->display($name, $context);
    }

    public function isTemplateFresh($name, $time)
    {
        return $this->twig->isTemplateFresh($name, $time);
    }

    public function resolveTemplate($names)
    {
        return $this->twig->resolveTemplate($names);
    }

    public function clearTemplateCache()
    {
        $this->twig->clearTemplateCache();
    }

    public function clearCacheFiles()
    {
        return $this->twig->clearCacheFiles();
    }

    public function getLexer()
    {
        return $this->twig->getLexer();
    }

    public function setLexer(\Twig_LexerInterface $lexer)
    {
        return $this->twig->setLexer($lexer);
    }

    public function tokenize($source, $name = null)
    {
        return $this->twig->tokenize($source, $name);
    }

    public function getParser()
    {
        return $this->twig->getParser();
    }

    public function setParser(\Twig_ParserInterface $parser)
    {
        return $this->twig->setParser($parser);
    }

    public function parse(\Twig_TokenStream $stream)
    {
        return $this->twig->parse($stream);
    }

    public function getCompiler()
    {
        return $this->twig->getCompiler($stream);
    }

    public function setCompiler(\Twig_CompilerInterface $compiler)
    {
        return $this->twig->setCompiler($compiler);
    }

    public function compile(\Twig_NodeInterface $node)
    {
        return $this->twig->compile($node);
    }

    public function setLoader(\Twig_LoaderInterface $loader)
    {
        return $this->twig->setLoader($loader);
    }

    public function getLoader()
    {
        return $this->twig->getLoader();
    }

    public function setCharset($charset)
    {
        return $this->twig->setCharset($charset);
    }

    public function getCharset()
    {
        return $this->twig->getCharset();
    }

    public function initRuntime()
    {
        return $this->twig->initRuntime();
    }

    public function hasExtension($name)
    {
        return $this->twig->hasExtension($name);
    }

    public function getExtension($name)
    {
        return $this->twig->getExtension($name);
    }

    public function addExtension(\Twig_ExtensionInterface $extension)
    {
        return $this->twig->addExtension($extension);
    }

    public function removeExtension($name)
    {
        return $this->twig->removeExtension($name);
    }

    public function setExtensions(array $extensions)
    {
        return $this->twig->setExtensions($extensions);
    }

    public function getExtensions()
    {
        return $this->twig->getExtensions();
    }

    public function addTokenParser(\Twig_TokenParserInterface $parser)
    {
        return $this->twig->addTokenParser($parser);
    }

    public function getTokenParsers()
    {
        return $this->twig->getTokenParsers();
    }

    public function getTags()
    {
        return $this->twig->getTags();
    }

    public function addNodeVisitor(\Twig_NodeVisitorInterface $visitor)
    {
        return $this->twig->addNodeVisitor($visitor);
    }

    public function getNodeVisitors()
    {
        return $this->twig->getNodeVisitors();
    }

    public function addFilter($name, $filter = null)
    {
        return $this->twig->addFilter($name, $filter);
    }

    public function getFilter($name)
    {
        return $this->twig->getFilter($name);
    }

    public function registerUndefinedFilterCallback($callable)
    {
        return $this->twig->registerUndefinedFilterCallback($callable);
    }

    public function getFilters()
    {
        return $this->twig->getFilters();
    }

    public function addTest($name, $test = null)
    {
        return $this->twig->addTest($name, $test);
    }

    public function getTests()
    {
        return $this->twig->getTests();
    }

    public function getTest($name)
    {
        return $this->twig->getTest($name);
    }

    public function addFunction($name, $function = null)
    {
        return $this->twig->addFunction($name, $function);
    }

    public function getFunction($name)
    {
        return $this->twig->getFunction($name);
    }

    public function registerUndefinedFunctionCallback($callable)
    {
        return $this->twig->registerUndefinedFunctionCallback($callable);
    }

    public function getFunctions()
    {
        return $this->twig->getFunctions();
    }

    public function addGlobal($name, $value)
    {
        return $this->twig->addGlobal($name, $value);
    }

    public function getGlobals()
    {
        return $this->twig->getGlobals();
    }

    public function mergeGlobals(array $context)
    {
        return $this->twig->mergeGlobals($context);
    }

    public function getUnaryOperators()
    {
        return $this->twig->getUnaryOperators();
    }

    public function getBinaryOperators()
    {
        return $this->twig->getBinaryOperators();
    }

    public function computeAlternatives($name, $items)
    {
        return $this->twig->computeAlternatives($name, $items);
    }
}
