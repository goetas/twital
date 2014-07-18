<?php
namespace Goetas\Twital\Tests;

use Goetas\Twital\EventSubscriber\FixTwigExpressionSubscriber;
use Goetas\Twital\Extension\FullCompatibilityTwigExtension;
use Goetas\Twital\SourceAdapter\XHTMLAdapter;
use Goetas\Twital\SourceAdapter\XMLAdapter;
use Goetas\Twital\Twital;
use Goetas\Twital\SourceAdapter\HTML5Adapter;
use Goetas\Twital\TwitalLoader;
use Goetas\Twital\EventSubscriber\ContextAwareEscapingSubscriber;

class ContextAwareEscapingTest extends \PHPUnit_Framework_TestCase
{

    private $twital;

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        $this->twital = new Twital();
    }


    /**
     * @dataProvider getData
     */
    public function testHTML5SourceAdapter($source, $expected)
    {
        $sourceAdapter = new HTML5Adapter();

        $expectedDom = $sourceAdapter->load($expected);
        $expectedStr = $sourceAdapter->dump($expectedDom);

        $compiled = $this->twital->compile($sourceAdapter, $this->wrap($source, false));
        $this->assertEquals($this->wrap($expectedStr, false), $compiled);
    }

    /**
     * @dataProvider getData
     */
    public function testHTML5SourceAdapterNotWrapped($source, $expected)
    {
        $sourceAdapter = new HTML5Adapter();

        $expectedDom = $sourceAdapter->load($expected);
        $expectedStr = $sourceAdapter->dump($expectedDom);

        $compiled = $this->twital->compile($sourceAdapter, $source);
        $this->assertEquals($expectedStr, $compiled);
    }

    /**
     * @dataProvider getData
     */
    public function testXHTMLSourceAdapter($source, $expected)
    {
        $sourceAdapter = new XHTMLAdapter();

        $compiled = $this->twital->compile($sourceAdapter, $this->wrap($source));
        $this->assertEquals($this->wrap($expected), $compiled);
    }

    /**
     * @dataProvider getData
     */
    public function testXMLSourceAdapter($source, $expected)
    {
        $sourceAdapter = new XMLAdapter();

        $compiled = $this->twital->compile($sourceAdapter, $this->wrap($source));
        $this->assertEquals($this->wrap($expected), $compiled);
    }
    protected function wrap($html, $addNs = true)
    {
        return "<html". ($addNs?" xmlns=\"http://www.w3.org/1999/xhtml\"":"")."><body>$html</body></html>";
    }

    public function getData()
    {
        return array(
            // script escaping
            array(
                '<script type="text/javascript">alert(\'{{ foo }}\')</script>',
                '<script type="text/javascript">{% autoescape \'js\' %}alert(\'{{ foo }}\'){% endautoescape %}</script>',
            ),
            array(
                '<script>alert(\'{{ foo }}\')</script>',
                '<script>{% autoescape \'js\' %}alert(\'{{ foo }}\'){% endautoescape %}</script>',
            ),
            array(
                '<script>/*<![CDATA[*/if (a > a && c) alert(1);/*]]>*/</script>',
                '<script>/*<![CDATA[*/if (a > a && c) alert(1);/*]]>*/</script>',
            ),
            array(
                '<script>/*<![CDATA[*/if (a > a && {{ foo }}) alert(1);/*]]>*/</script>',
                '<script>{% autoescape \'js\' %}/*<![CDATA[*/if (a > a && {{ foo }}) alert(1);/*]]>*/{% endautoescape %}</script>',
            ),

            // CSS escaping
            array(
                '<style type="text/css">p { font-family: "{{ foo }}"; }</style>',
                '<style type="text/css">{% autoescape \'css\' %}p { font-family: "{{ foo }}"; }{% endautoescape %}</style>',
            ),
            array(
                '<style>p { font-family: "{{ foo }}"; }</style>',
                '<style>{% autoescape \'css\' %}p { font-family: "{{ foo }}"; }{% endautoescape %}</style>',
            ),
            array(
                '<style>/*<![CDATA[*/p > a { font-family: "Arial"; }/*]]>*/</style>',
                '<style>/*<![CDATA[*/p > a { font-family: "Arial"; }/*]]>*/</style>',
            ),
            array(
                '<style>/*<![CDATA[*/p > a { font-family: "{{ foo }}"; }/*]]>*/</style>',
                '<style>{% autoescape \'css\' %}/*<![CDATA[*/p > a { font-family: "{{ foo }}"; }/*]]>*/{% endautoescape %}</style>',
            ),

            // inline script escaping
            array(
                '<a href="javascript:{{ foo }}">bar</a>',
                '<a href="javascript:{{ ( foo )  | escape(\'js\') }}">bar</a>',
            ),

            // URL escaping
            array(
                '<img src="{{ foo }}"/>',
                '<img src="{{ foo }}"/>'
            ),
            array(
                '<a href="{{ foo }}">bar</a>',
                '<a href="{{ foo }}">bar</a>',
            ),

            array(
                '<a href="foo?q={{ foo }}">bar</a>',
                '<a href="foo?q={{ ( foo )  | escape(\'url\') }}">bar</a>',
            ),

            array(
                '<img src="a.gif?a=b&amp;{{ foo }}"/>',
                '<img src="a.gif?a=b&amp;{{ ( foo )  | escape(\'url\') }}"/>'
            ),
        );
    }
}
