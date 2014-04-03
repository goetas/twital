<?php
namespace Goetas\Twital\Tests;

use Goetas\Twital\Twital;
use Goetas\Twital\SourceAdapter\XMLAdapter;


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

    protected abstract function getSourceAdapter();

    /**
     * @dataProvider getData
     */
    public function testVisitNode($source, $expected)
    {
        $compiled = $this->twital->compile($this->sourceAdapter, $source);
        $this->assertEquals($expected, $compiled);
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
            array('<t:omit><![CDATA[{{ foo }}]]></t:omit>', '<![CDATA[{{ foo }}]]>'),

            // array('<![CDATA[{{ foo }}]]>', '<![CDATA[{{ foo }}]]>'), // not possible
        );
    }

    public function getDataFormTemplates()
    {
        $all = glob(__DIR__."/templates/*.xml");
        $data = array();
        foreach ($all as $file) {

            $source = file_get_contents($file);
            $expected = trim(file_get_contents(substr($file, 0, -4).".twig"));

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
        $this->assertEquals($expected, $compiled);
    }

}


