<?php
namespace Goetas\Twital\Tests;

use Goetas\Twital\EventSubscriber\FixTwigExpressionSubscriber;
use Goetas\Twital\Extension\FullCompatibilityTwigExtension;
use Goetas\Twital\SourceAdapter\XHTMLAdapter;
use Goetas\Twital\SourceAdapter\XMLAdapter;
use Goetas\Twital\Twital;
use Goetas\Twital\SourceAdapter\HTML5Adapter;


class FullCompatibilityTwigTest extends \PHPUnit_Framework_TestCase
{
    private $twital;

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        $this->twital = new Twital();
        $this->twital->addExtension(new FullCompatibilityTwigExtension());

    }

    public function testListenerPriority()
    {
        $eventDispatcher = $this->twital->getEventDispatcher();

        $preLoad = $eventDispatcher->getListeners('compiler.pre_load');
        $this->assertEquals(array(new FixTwigExpressionSubscriber(), 'addPlaceholder'), reset($preLoad));

        $postDump = $eventDispatcher->getListeners('compiler.post_dump');
        $this->assertEquals(array(new FixTwigExpressionSubscriber(), 'removePlaceholder'), end($postDump));
    }

    /**
     * @dataProvider getData
     */
    public function testHTML5SourceAdapter($source)
    {
        $sourceAdapter = new HTML5Adapter();

        $compiled = $this->twital->compile($sourceAdapter, $source);
        $this->assertEquals($source, $compiled);
    }

    /**
     * @dataProvider getData
     */
    public function testXHTMLSourceAdapter($source)
    {
        $sourceAdapter = new XHTMLAdapter();

        $compiled = $this->twital->compile($sourceAdapter, $source);
        $this->assertEquals($source, $compiled);
    }

    /**
     * @dataProvider getData
     */
    public function testXMLSourceAdapter($source)
    {
        $sourceAdapter = new XMLAdapter();

        $compiled = $this->twital->compile($sourceAdapter, $source);
        $this->assertEquals($source, $compiled);
    }

    public function getData()
    {
        return array(
            // operators

            array('<div>{% if foo > 5 and bar < 8 and bar & 4 %}foo{% endif %}</div>'),
            array('<div>{{ foo > 5 and bar < 8 and bar & 4 ? "foo" }}</div>'),
            array('<div>{# foo > 5 and bar < 8 and bar & 4 ? "foo" #}</div>'),

            array("<div>{% '{%' and '%}' and '{{' and '}}' and '{#' and '#}' %}</div>"),
            array("<div>{{ '{%' and '%}' and '{{' and '}}' and '{#' and '#}' }}</div>"),
            array("<div>{# '{%' and '%}' and '{{' and '}}' and '{#' and '#}' #}</div>"),

            array("<div>{{ '}}\\'}}' > 5 & 4 }}</div>"),
            array("<div>{{ '{%\\'%}\\\\\\'}}' > 5 & 4 }}</div>"),

            array('<title>{% block title "title" %}</title>'),
            array('<title name="{% block title "title" %}">foo</title>'),

            array('<div {{ "foo"|raw }}>foo</div>'),
            array('<{{ tagname }}>foo</{{ tagname }}>'),
            array('<div{{ attributes }}>foo</div>'),
            array('<div{# attributes #} class="class {{ classes|filter("foo", "bar\\"") }}">foo</div>'),
            array('<div{{ foo > 5 or foo < 8 }} class="class {{ "foo" < "bar" and 5 > 3 }}">foo</div>'),
            array('<div {{ attributes({"foo": "bar"}) }}>foo</div>'),
            array('<label{% for attrname, attrvalue in label_attr %} {{ attrname }}="{{ attrvalue }}"{% endfor %}>foo</label>'),
            array('<div {{ block(\'widget_attributes\') }}{% if value is defined %} value="{{ value }}"{% endif %}{% if checked %} checked="checked"{% endif %}>foo</div>'),
        );
    }
}
