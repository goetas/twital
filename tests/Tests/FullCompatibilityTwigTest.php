<?php
namespace Goetas\Twital\Tests;

use Goetas\Twital\EventDispatcher\SourceEvent;
use Goetas\Twital\EventSubscriber\FixTwigExpressionSubscriber;
use Goetas\Twital\Extension\FullCompatibilityTwigExtension;
use Goetas\Twital\Twital;
use Goetas\Twital\SourceAdapter\HTML5Adapter;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class FullCompatibilityTwigTest extends \PHPUnit_Framework_TestCase
{
    private $templateSubscriber;
    private $twital;

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        $this->templateSubscriber = new DebugTemplateSubscriber();

        $this->twital = new Twital();
        $this->twital->addExtension(new FullCompatibilityTwigExtension());
        $this->twital->getEventDispatcher()->addSubscriber($this->templateSubscriber);
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
    public function testHTML5SourceAdapter($source, $expected = null)
    {
        $sourceAdapter = new HTML5Adapter();

        $compiled = $this->twital->compile($sourceAdapter, $source);
        $this->assertEquals($expected !== null ? $expected : $source, $compiled, 'PRE: '.$this->templateSubscriber->preLoadTemplate."\n\nPOST: ".$this->templateSubscriber->postDumpTemplate);
    }

    public function getData()
    {
        return array(
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
            array('<div {% if foo %}class="foo"{% endif %}>foo</div>'),
            array('<label{% for attrname, attrvalue in label_attr %} {{ attrname }}="{{ attrvalue }}"{% endfor %}>foo</label>'),
            array('<div {{ block(\'widget_attributes\') }}{% if value is defined %} value="{{ value }}"{% endif %}{% if checked %} checked="checked"{% endif %}>foo</div>'),

            array(file_get_contents(__DIR__.'/templates/web_profiler_js.html.twig')),
            array(file_get_contents(__DIR__.'/templates/logger.html.twig')),
            array('test <div t:if="foo">{{ foo }}</div> test <div t:if="bar">{{ bar }}</div> test', 'test {% if foo %}<div>{{ foo }}</div>{% endif %} test {% if bar %}<div>{{ bar }}</div>{% endif %} test'),
        );
    }
}

class DebugTemplateSubscriber implements EventSubscriberInterface
{
    public $preLoadTemplate;
    public $postDumpTemplate;

    public static function getSubscribedEvents()
    {
        return array(
            'compiler.pre_load' => array('onPreLoad'),
            'compiler.post_dump' => array('onPostDump'),
        );
    }

    public function onPreLoad(SourceEvent $event)
    {
        $this->preLoadTemplate = $event->getTemplate();
    }

    public function onPostDump(SourceEvent $event)
    {
        $this->postDumpTemplate = $event->getTemplate();
    }
}
