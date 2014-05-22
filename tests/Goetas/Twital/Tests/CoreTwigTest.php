<?php
namespace Goetas\Twital\Tests;

use Goetas\Twital\SourceAdapter\XHTMLAdapter;
use Goetas\Twital\SourceAdapter\XMLAdapter;
use Goetas\Twital\Twital;
use Goetas\Twital\SourceAdapter\HTML5Adapter;


class CoreTwigTest extends \PHPUnit_Framework_TestCase
{
    private $twital;
    private $sourceAdapter;

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        $this->twital = new Twital();
        $this->sourceAdapter = new HTML5Adapter();
    }

    /**
     * @dataProvider getData
     */
    public function testTwigTemplate($source, $expected)
    {
        $compiled = $this->twital->compile($this->sourceAdapter, $source);
        $this->assertEquals($expected, $compiled);
    }

    public function getData()
    {
        return array(
            // operators

            array('<div>{% if foo > 5 and bar < 8 and bar & 4 %}foo{% endif %}</div>', '<div>{% if foo > 5 and bar < 8 and bar & 4 %}foo{% endif %}</div>'),
            array('<div>{{ foo > 5 and bar < 8 and bar & 4 ? "foo" }}</div>', '<div>{{ foo > 5 and bar < 8 and bar & 4 ? "foo" }}</div>'),
            array('<div>{# foo > 5 and bar < 8 and bar & 4 ? "foo" #}</div>', '<div>{# foo > 5 and bar < 8 and bar & 4 ? "foo" #}</div>'),

            array("<div>{% '{%' and '%}' and '{{' and '}}' and '{#' and '#}' %}</div>", "<div>{% '{%' and '%}' and '{{' and '}}' and '{#' and '#}' %}</div>"),
            array("<div>{{ '{%' and '%}' and '{{' and '}}' and '{#' and '#}' }}</div>", "<div>{{ '{%' and '%}' and '{{' and '}}' and '{#' and '#}' }}</div>"),

            array("<div>{# '{%' and '%}' and '{{' and '}}' and '{#' and '#}' #}</div>", "<div>{# '{%' and '%}' and '{{' and '}}' and '{#' and '#}' #}</div>"),
        );
    }
}


