<?php
namespace Goetas\Twital\Tests;

use Goetas\Twital\TwitalLoader;
use Goetas\Twital\SourceAdapter\XMLAdapter;
use Goetas\Twital\Tests\Twig\StringLoader;

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

        $twitalLoader = new TwitalLoader(new StringLoader(), null, false);
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
            array('<div><img src="{{ \'abc\'}}"/></div>', '<div><img src="abc"/></div>'),
            array('<div class="bar" t:attr="class=false">content</div>', '<div>content</div>'),
            array('<div class="bar" t:attr="class=\'foo\'">content</div>', '<div class="foo">content</div>'),
            array('<div t:attr="condition?class=\'foo\'">content</div>', '<div class="foo">content</div>', array('condition'=>1)),
            array('<div class="bar" t:attr="condition?class=\'foo\'">content</div>', '<div class="bar">content</div>', array('condition'=>0)),
            array('<div class="bar" t:attr="condition?class=\'foo\'">content</div>', '<div class="foo">content</div>', array('condition'=>1)),

            array('<math xmlns="http://www.w3.org/1998/Math/MathML">a</math>', '<math xmlns="http://www.w3.org/1998/Math/MathML">a</math>'),
            array('<m:math xmlns:m="http://www.w3.org/1998/Math/MathML">a</m:math>', '<m:math xmlns:m="http://www.w3.org/1998/Math/MathML">a</m:math>'),
            array('<m:math xmlns:m="http://www.w3.org/1998/Math/MathML" m:expr="e">a</m:math>', '<m:math xmlns:m="http://www.w3.org/1998/Math/MathML" m:expr="e">a</m:math>'),

            array('<div t:attr="condition?class=\'foo\'">content</div>', '<div>content</div>', array('condition'=>0)),

            array('<div t:attr-append="condition?class=\'foo\'">content</div>', '<div class="foo">content</div>', array('condition'=>1)),
            array('<div class="foo" t:attr-append="condition?class=\'bar\'">content</div>', '<div class="foobar">content</div>', array('condition'=>1)),
            array('<div t:attr-append="condition?class=\'foo\', condition?class=\'bar\'">content</div>', '<div class="foobar">content</div>', array('condition'=>1)),

            array('<div t:omit="condition">content</div>', 'content', array('condition'=>1)),
            array('<div t:omit="condition">content</div>', '<div>content</div>', array('condition'=>0)),
            array('<div t:omit="true">content</div>', 'content'),

            array('<img src="{{a}}" />', '<img src="/a/x.jpg"/>', array('a' => '/a/x.jpg')),
            array('<img src="{{ true ? "/b/x.jpg" }}" />', '<img src="/b/x.jpg"/>',),
            array('<img src="{% if true %}/c/x.jpg{% endif %}" />', '<img src="/c/x.jpg"/>'),
            array('<img src="{% if true %}{{a}}{% else %}{{b}}{% endif %}" />', '<img src="/d/x.jpg"/>', array('a' => '/d/x.jpg', 'b' => '/b/x.jpg')),
        );
    }

    public function testAttributeHash()
    {

        $source = <<<EOT
<div>
                <ol>
                    <li t:attr="true ? style='display:none'"/>
                </ol>
                <div>
                    <span>yyy</span>
                </div>
                <div>
                    <input t:attr="true ? checked='checked'"/>
                </div>
            </div>
EOT;

        $rendered = $this->twig->render($source);
        $expected = <<<EOT
<div>
                <ol>
                    <li style="display:none"/>
                </ol>
                <div>
                    <span>yyy</span>
                </div>
                <div>
                    <input checked="checked"/>
                </div>
            </div>
EOT;
        $this->assertEquals($expected, $rendered);
    }

    public function getInvalidData()
    {
        return array(
            array('<div t:attr="!class?\'foo\'">content</div>'),
            array('<div t:attr="class?\'foo\'?X">content</div>'),
        );
    }

    /**
     * @dataProvider getInvalidData
     * @expectedException Exception
     */
    public function testInvalidVisitAttribute($source)
    {
        $this->twig->render($source);
    }
}
