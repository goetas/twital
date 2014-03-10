<?php
namespace Goetas\Twital\Tests;

use Goetas\Twital\Twital;
use Goetas\Twital\SourceAdapter\XMLAdapter;


class CoreAttributeTest extends \PHPUnit_Framework_TestCase
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
    public function testVisitAttribute($source, $expected)
    {
        $compiled = $this->twital->compile($this->sourceAdapter, $source);
        $this->assertEquals($expected, $compiled);
    }

    public function getData()
    {
        return array(
            array('<div t:if="test">content</div>', '{% if test %}<div>content</div>{% endif %}'),
            array('<div t:if="true">content</div>', '<div>content</div>'),

            array('<div t:for="foo">content</div>', '{% for foo %}<div>content</div>{% endfor %}'),

            array('<div t:set="foo = 1">content</div>', '{% set foo = 1 %}<div>content</div>'),
            array('<div t:capture="foo">content</div>', '{% set foo %}<div>content</div>{% endset %}'),

            array('<div t:content="foo">content</div>', '<div>{{ foo }}</div>'),

        );
    }
}


