<?php
namespace Goetas\Twital\Tests;

use Goetas\Twital\SourceAdapter\HTML5Adapter;
use Goetas\Twital\TwitalLoader;
use Twig\Environment;
use Twig\Loader\ArrayLoader;

class Html5DynamicAttrAttributeTest extends TestCase
{
    /**
     * @var Environment
     */
    protected $twig;

    /**
     * @var ArrayLoader
     */
    protected $loader;

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        parent::setUp();

        $this->loader = new ArrayLoader();
        $twitalLoader = new TwitalLoader($this->loader, null, false);
        $twitalLoader->addSourceAdapter("/.*/", new HTML5Adapter());

        $this->twig = new Environment($twitalLoader, array(
            'strict_variables' => true
        ));
    }

    /**
     * @dataProvider getData
     */
    public function testVisitAttribute($source, $expected, $vars = null)
    {
        $this->loader->setTemplate('template', $source);
        $rendered = $this->twig->render('template', $vars ?: array());
        $this->assertEquals($expected, $rendered);
    }

    public function getData()
    {
        return array(
            array('<option t:attr="selected=value">content</option>', '<option selected>content</option>', array('value' => true)),

            array('<option t:attr="selected=value">content</option>', '<option>content</option>', array('value' => false)),
            array('<option t:attr="selected=value">content</option>', '<option selected="1">content</option>', array('value' => 1)),
            array('<option t:attr="selected=value">content</option>', '<option selected="foo">content</option>', array('value' => 'foo')),

            array('<option t:attr="selected=value" t:attr-append="selected=\' aaa\'">content</option>', '<option selected="1 aaa">content</option>', array('value' => true)),
            array('<option t:attr="selected=value" t:attr-append="selected=false">content</option>', '<option selected="1">content</option>', array('value' => true)),

            array('<option t:attr="selected=value" t:attr-append="selected=\' aaa\'">content</option>', '<option selected=" aaa">content</option>', array('value' => false)),
            array('<option t:attr="selected=value" t:attr-append="selected=true">content</option>', '<option selected="1">content</option>', array('value' => false)),
            array('<option t:attr="selected=value" t:attr-append="selected=false">content</option>', '<option selected="">content</option>', array('value' => false)),

            array('<option t:attr="selected=value" t:attr-append="selected=\' aaa\'">content</option>', '<option selected="foo aaa">content</option>', array('value' => 'foo')),
            array('<option t:attr="selected=value" t:attr-append="selected=false">content</option>', '<option selected="foo">content</option>', array('value' => 'foo')),
            array('<option t:attr="selected=value" t:attr-append="selected=true">content</option>', '<option selected="foo1">content</option>', array('value' => 'foo')),
        );
    }
}
