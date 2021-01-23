<?php
namespace Goetas\Twital\Tests;

use Goetas\Twital\SourceAdapter\XMLAdapter;
use Goetas\Twital\Twital;
use PHPUnit\Framework\TestCase;

class CoreAttributeTest extends TestCase
{
    /**
     * Prepares the environment before running a test.
     */
    protected function setUp():void
    {
        parent::setUp();
        $this->twital = new Twital();
        $this->sourceAdapter = new XMLAdapter();
    }

    /**
     * @dataProvider getData
     */
    public function testVisitAttribute($source, $expected)
    {
        $compiled = $this->twital->compile($this->sourceAdapter, $source);
        $this->assertEquals($expected, $compiled);
    }

    public function getData()
    {
        return array(
            // if
            array('<div t:if="test">content</div>', '{% if test %}<div>content</div>{% endif %}'),
            array('<div><div t:if="test1">content1</div><div t:elseif="test2">content2</div></div>', '<div>{% if test1 %}<div>content1</div>{% elseif test2 %}<div>content2</div>{% endif %}</div>'),
            array('<div><div t:if="test1">content1</div>  <div t:elseif="test2">content2</div></div>', '<div>{% if test1 %}<div>content1</div>{% elseif test2 %}<div>content2</div>{% endif %}</div>'),
            array('<div><div t:if="test1">content1</div>  <div t:elseif="test2">content2</div>  <div t:elseif="test3">content3</div></div>', '<div>{% if test1 %}<div>content1</div>{% elseif test2 %}<div>content2</div>{% elseif test3 %}<div>content3</div>{% endif %}</div>'),
            array('<div><div t:if="test1">content1</div><div t:else="">content2</div></div>', '<div>{% if test1 %}<div>content1</div>{% else %}<div>content2</div>{% endif %}</div>'),
            array('<div><div t:if="test1">content1</div><div t:elseif="test2">content2</div><div t:else="">content3</div></div>', '<div>{% if test1 %}<div>content1</div>{% elseif test2 %}<div>content2</div>{% else %}<div>content3</div>{% endif %}</div>'),
            // for
            array('<div t:for="foo">content</div>', '{% for foo %}<div>content</div>{% endfor %}'),
            // set
            array('<div t:set="foo = 1">content</div>', '{% set foo = 1 %}<div>content</div>'),
            array('<div t:capture="foo">content</div>', '{% set foo %}<div>content</div>{% endset %}'),
            // content
            array('<div t:content="foo">content</div>', '<div>{{ foo }}</div>'),
            // mixed
            array('<div t:if="cond" t:for="foo">content</div>', '{% if cond %}{% for foo %}<div>content</div>{% endfor %}{% endif %}'),
            array('<div t:for="foo" t:if="cond">content</div>', '{% for foo %}{% if cond %}<div>content</div>{% endif %}{% endfor %}'),
            // omit
            array('<div t:omit="cond">content</div>', '{% set __tmp_omit = cond %}{% if not __tmp_omit %}<div>{% endif %}content{% if not __tmp_omit %}</div>{% endif %}'),
            array('<div xmlns="xxx" t:omit="cond"><span>content</span></div>', '{% set __tmp_omit = cond %}{% if not __tmp_omit %}<div xmlns="xxx">{% endif %}<span>content</span>{% if not __tmp_omit %}</div>{% endif %}'),

            array('<div t:if="cond" t:omit="true">content</div>', '{% if cond %}{% set __tmp_omit = true %}{% if not __tmp_omit %}<div>{% endif %}content{% if not __tmp_omit %}</div>{% endif %}{% endif %}'),

        );
    }
}
