<?php
namespace Goetas\Twital\Tests;

use Goetas\Twital\Twital;

abstract class CoreNodesTest extends \PHPUnit_Framework_TestCase
{
    protected $twital;
    protected $sourceAdapter;
    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        parent::setUp();
        $this->twital = new Twital();
        $this->sourceAdapter = $this->getSourceAdapter();
    }

    abstract protected function getSourceAdapter();

    /**
     * @dataProvider getData
     */
    public function testVisitNode($source, $expected)
    {
        $compiled = $this->twital->compile($this->sourceAdapter, $source);
        $this->assertEquals($expected, $compiled);
    }

    /**
     * @dataProvider getAttributes
     */
    public function testNonEmptyHtmlAttributes($attributeName)
    {
        $source = '<div ' . $attributeName .'="">foo</div>';

        $compiled = $this->twital->compile($this->sourceAdapter, $source);
        $this->assertEquals($source, $compiled);
    }

    public function getAttributes()
    {
        return array_map(function ($v) {
            return array($v);
        }, array (
                'href',
                'hreflang',
                'http-equiv',
                'icon',
                'id',
                'keytype',
                'kind',
                'label',
                'lang',
                'language',
                'list',
                'maxlength',
                'media',
                'method',
                'name',
                'placeholder',
                'rel',
                'rows',
                'rowspan',
                'sandbox',
                'spellcheck',
                'scope',
                'seamless',
                'shape',
                'size',
                'sizes',
                'span',
                'src',
                'srcdoc',
                'srclang',
                'srcset',
                'start',
                'step',
                'style',
                'summary',
                'tabindex',
                'target',
                'title',
                'type',
                'value',
                'width',
                'border',
                'charset',
                'cite',
                'class',
                'code',
                'codebase',
                'color',
                'cols',
                'colspan',
                'content',
                'coords',
                'data',
                'datetime',
                'default',
                'dir',
                'dirname',
                'enctype',
                'for',
                'form',
                'formaction',
                'headers',
                'height',
                'accept',
                'accept-charset',
                'accesskey',
                'action',
                'align',
                'alt',
                'bgcolor'
        ));
    }

    public function getData()
    {
        return array(

            array('<t:include from-exp="foo"/>', '{% include foo %}'),
            array('<t:include from="aaa.html"/>', '{% include "aaa.html" %}'),
            array('<t:include from="aaa.html" ignore-missing="true"/>', '{% include "aaa.html" ignore missing %}'),
            array('<t:include from="aaa.html" sandboxed="true"/>', '{% include "aaa.html" sandboxed = true %}'),
            array('<t:include from="aaa.html" with="{aaa:bbb}"/>', '{% include "aaa.html" with {aaa:bbb} %}'),
            array('<t:include from="aaa.html" with="{aaa:bbb} only"/>', '{% include "aaa.html" with {aaa:bbb} only %}'),
            array('<t:include from="aaa.html" with="{aaa:bbb}" only="true"/>', '{% include "aaa.html" with {aaa:bbb} only %}'),

            array('<t:import from="forms.html" as="forms"/>','{% import "forms.html" as forms %}'),
            array('<t:import from="forms.html" aliases="input as input_field, textarea"/>', '{% from "forms.html" import input as input_field, textarea %}'),

            array('<t:omit>foo</t:omit>', 'foo'),
            array('<t:omit><div>foo</div></t:omit>', '<div>foo</div>'),
            array('<div><t:omit t:if="0">foo</t:omit>bar</div>', '<div>{% if 0 %}foo{% endif %}bar</div>'),
            array('<div><![CDATA[aa]]></div>', '<div><![CDATA[aa]]></div>'),
            array('<div><!-- foo --></div>', '<div><!-- foo --></div>'),

            //CustomNamespaceRawSubscriber & FixHtmlEntitiesInExpressionSubscriber
            array('<div>{{ foo > 5 }}</div>', '<div>{{ foo > 5 }}</div>'),
            array('<div t:if="foo > 5">foo</div>', '{% if foo > 5 %}<div>foo</div>{% endif %}'),
            array('<div if="{{ \'foo > 5\' }}">foo</div>', '<div if="{{ \'foo > 5\' }}">foo</div>'),

            array('<div>{{ foo }}&amp;foo</div>', '<div>{{ foo }}&amp;foo</div>'),
            array('<div attr="{{ foo }}&amp;foo">test</div>', '<div attr="{{ foo }}&amp;foo">test</div>'),
            array('<t:omit><![CDATA[{{ foo }}]]></t:omit>', '<![CDATA[{{ foo }}]]>'),

            array('<div>{% if foo > 5 and bar < 8 and bar & 4 %}foo{% endif %}</div>', '<div>{% if foo > 5 and bar < 8 and bar & 4 %}foo{% endif %}</div>'),
            array('<div>{{ foo > 5 and bar < 8 and bar & 4 ? "foo" }}</div>', '<div>{{ foo > 5 and bar < 8 and bar & 4 ? "foo" }}</div>'),
            array('<div>{# foo > 5 and bar < 8 and bar & 4 ? "foo" #}</div>', '<div>{# foo > 5 and bar < 8 and bar & 4 ? "foo" #}</div>'),

            array("<div>{% '{%' and '%}' and '{{' and '}}' and '{#' and '#}' %}</div>", "<div>{% '{%' and '%}' and '{{' and '}}' and '{#' and '#}' %}</div>"),
            array("<div>{{ '{%' and '%}' and '{{' and '}}' and '{#' and '#}' }}</div>", "<div>{{ '{%' and '%}' and '{{' and '}}' and '{#' and '#}' }}</div>"),
            array("<div>{# '{%' and '%}' and '{{' and '}}' and '{#' and '#}' #}</div>", "<div>{# '{%' and '%}' and '{{' and '}}' and '{#' and '#}' #}</div>"),

            array('<div>{% block title "title" %}</div>', '<div>{% block title "title" %}</div>'),
            array("<div>{% foo \n bar %}</div>", "<div>{% foo \n bar %}</div>"),
            array("<div>{{ foo \n bar }}</div>", "<div>{{ foo \n bar }}</div>"),
            array('<div name="{% block title "title" %}">foo</div>', '<div name="{% block title "title" %}">foo</div>'),

            // array('<![CDATA[{{ foo }}]]>', '<![CDATA[{{ foo }}]]>'), // not possible
        );
    }

    public function getDataFormTemplates()
    {
        $all = glob(__DIR__."/templates/*.xml");
        $data = array();
        foreach ($all as $file) {

            $source = file_get_contents($file);
            $expected = file_get_contents(substr($file, 0, -4).".twig");

            $data[] = array(
                $source,
                $expected,
            );
        }

        return $data;
    }

    /**
     * @dataProvider getDataFormTemplates
     */
    public function testVisitNodeTemplates($source, $expected)
    {
        $compiled = $this->twital->compile($this->sourceAdapter, $source);

        $cleanup = function ($str) {
        	return preg_replace("/\s+/", "", $str);
        };
        $this->assertEquals($cleanup($expected), $cleanup($compiled));
    }

}
