<?php
namespace Goetas\Twital\Tests;

use Goetas\Twital\TwitalLoader;
use Goetas\Twital\Twital;
use Goetas\Twital\SourceAdapter\XMLAdapter;


class DynamicAttrAttributeTest extends \PHPUnit_Framework_TestCase
{
    /**
     *
     * @var \Twig_Environment
     */
    protected $twig;

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        parent::setUp();

        $twitalLoader = new TwitalLoader(new \Twig_Loader_String(), null, false);
        $twitalLoader->addSourceAdapter("/.*/", new XMLAdapter());

        $this->twig = new \Twig_Environment($twitalLoader);
    }

    /**
     * @dataProvider getData
     */
    public function testVisitAttribute($source, $expected, $vars=null)
    {
        $rendered = $this->twig->render($source, $vars?:array());
        $this->assertEquals($expected, $rendered);
    }

    public function getData()
    {
        return array(
            array('<div t:attr="class=\'foo\'">content</div>', '<div class="foo">content</div>'),
            array('<div t:attr="condition?class=\'foo\'">content</div>', '<div class="foo">content</div>', array('condition'=>1)),
            array('<div t:attr="condition?class=\'foo\'">content</div>', '<div>content</div>', array('condition'=>0)),

            array('<div t:attr-append="condition?class=\'foo\'">content</div>', '<div class="foo">content</div>', array('condition'=>1)),
            array('<div class="foo" t:attr-append="condition?class=\'bar\'">content</div>', '<div class="foobar">content</div>', array('condition'=>1)),
            array('<div t:attr-append="condition?class=\'foo\', condition?class=\'bar\'">content</div>', '<div class="foobar">content</div>', array('condition'=>1)),



            array('<div t:omit="condition">content</div>', 'content', array('condition'=>1)),
            array('<div t:omit="condition">content</div>', '<div>content</div>', array('condition'=>0)),
            array('<div t:omit="true">content</div>', 'content'),
        );
    }
}


