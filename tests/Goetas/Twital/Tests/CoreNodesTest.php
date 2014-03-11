<?php
namespace Goetas\Twital\Tests;

use Goetas\Twital\Twital;
use Goetas\Twital\SourceAdapter\XMLAdapter;


class CoreNodesTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        parent::setUp();
        $this->twital = new Twital();
        $this->sourceAdapter = new XMLAdapter();
    }

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
        );
    }

    public function getDataFormTemplates()
    {
        $all = glob(__DIR__."/templates/*.xml");
        $data = array();
        foreach ($all as $file) {
            $data[] = array(
                trim(file_get_contents($file)),
                str_replace(array("\r\n","\r"), "\n",trim(file_get_contents(substr($file, 0, -4).".twig"))),
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


