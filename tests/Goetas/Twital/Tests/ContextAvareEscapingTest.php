<?php
namespace Goetas\Twital\Tests;

use Goetas\Twital\EventSubscriber\FixTwigExpressionSubscriber;
use Goetas\Twital\Extension\FullCompatibilityTwigExtension;
use Goetas\Twital\SourceAdapter\XHTMLAdapter;
use Goetas\Twital\SourceAdapter\XMLAdapter;
use Goetas\Twital\Twital;
use Goetas\Twital\SourceAdapter\HTML5Adapter;
use Goetas\Twital\TwitalLoader;

class ContextAvareEscapingTest extends \PHPUnit_Framework_TestCase
{

    private $twital;

    private $twig;

    private $loader;

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        $this->twital = new Twital();
        $this->loader = new TwitalLoader(new \Twig_Loader_String(), $this->twital);
        $this->twig = new \Twig_Environment($this->loader);
    }

    /**
     * @dataProvider getData
     */
    public function testHTML5SourceAdapter($source, $expected, $renderedExpected = null, $vars = array())
    {
        $sourceAdapter = new HTML5Adapter();

        $expectedDom = $sourceAdapter->load($expected);
        $expectedStr = $sourceAdapter->dump($expectedDom);

        $compiled = $this->twital->compile($sourceAdapter, $source);
        $this->assertEquals($expectedStr, $compiled);

        if ($renderedExpected) {
            $rendered = $this->twig->render($compiled, $vars);
            $this->assertEquals($renderedExpected, $rendered);
        }
    }

    /**
     * @dataProvider getData
     */
    public function testXHTMLSourceAdapter($source, $expected, $renderedExpected = null, $vars = array())
    {
        $sourceAdapter = new XHTMLAdapter();

        $compiled = $this->twital->compile($sourceAdapter, $source);
        $this->assertEquals($expected, $compiled);

        if ($renderedExpected) {
            $rendered = $this->twig->render($compiled, $vars);
            $this->assertEquals($renderedExpected, $rendered);
        }
    }

    /**
     * @dataProvider getData
     */
    public function testXMLSourceAdapter($source, $expected, $renderedExpected = null, $vars = array())
    {
        $sourceAdapter = new XMLAdapter();

        $compiled = $this->twital->compile($sourceAdapter, $source);
        $this->assertEquals($expected, $compiled);

        if ($renderedExpected) {
            $rendered = $this->twig->render($compiled, $vars);
            $this->assertEquals($renderedExpected, $rendered);
        }
    }

    public function getData()
    {
        return array(

            array(
                '<script type="text/javascript">alert(\'{{ foo }}\')</script>',
                '<script type="text/javascript">alert(\'{{ foo  | escape(\'js\') }}\')</script>',
                '<script type="text/javascript">alert(\'fo\x20\x27\x20\x22\x20o\')</script>',
                array(
                    'foo' => 'fo \' " o'
                )
            ),
            array(
                '<script>alert(\'{{ foo }}\')</script>',
                '<script>alert(\'{{ foo  | escape(\'js\') }}\')</script>',
                '<script>alert(\'fo\x20\x27\x20\x22\x20o\')</script>',
                array(
                    'foo' => 'fo \' " o'
                )
            ),

            array(
                '<style type="text/css">p { font-family: "{{ foo }}"; }</style>',
                '<style type="text/css">p { font-family: "{{ foo  | escape(\'css\') }}"; }</style>',
                '<style type="text/css">p { font-family: "\3C \2F style\3E \20 \A \20 foo"; }</style>',
                array(
                    'foo' => "</style> \n foo"
                )
            ),
            array(
                '<style>p { font-family: "{{ foo }}"; }</style>',
                '<style>p { font-family: "{{ foo  | escape(\'css\') }}"; }</style>',
                '<style>p { font-family: "\3C \2F style\3E \20 \A \20 foo"; }</style>',
                array(
                    'foo' => "</style> \n foo"
                )
            ),
            array(
                '<style>p { background: url({{ foo }}); }</style>',
                '<style>p { background: url({{ foo  | escape(\'css\') }}); }</style>',
                '<style>p { background: url(\3C \2F style\3E \20 \A \20 foo); }</style>',
                array(
                    'foo' => "</style> \n foo"
                )
            ),

            array(
                '<a href="{{ foo }}">bar</a>',
                '<a href="{{ foo  | escape(\'html_attr\') }}">bar</a>',
                '<a href="http&#x3A;&#x2F;&#x2F;www.example.com">bar</a>',
                array(
                    'foo' =>'http://www.example.com'
                )

            ),

            array(
                '<a href="foo?q={{ foo }}">bar</a>',
                '<a href="foo?q={{ foo  | escape(\'url\') }}">bar</a>',
                '<a href="foo?q=f%20%3E%3Coo">bar</a>',
                array(
                    'foo' =>'f ><oo'
                )
            ),
            array(
                '<area href="{{ foo }}">bar</area>',
                '<area href="{{ foo  | escape(\'html_attr\') }}">bar</area>'
            ),
            array(
                '<link href="{{ foo }}"/>',
                '<link href="{{ foo  | escape(\'html_attr\') }}"/>'
            ),
            array(
                '<script src="{{ foo }}">alert(1)</script>',
                '<script src="{{ foo  | escape(\'html_attr\') }}">alert(1)</script>'
            ),
            array(
                '<img src="{{ foo }}"/>',
                '<img src="{{ foo  | escape(\'html_attr\') }}"/>'
            )
        )
        ;
    }
}
