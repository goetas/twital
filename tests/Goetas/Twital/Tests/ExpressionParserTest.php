<?php
namespace Goetas\Twital\Tests;

use Goetas\Twital\Twital;
use Goetas\Twital\SourceAdapter\XMLAdapter;
use Goetas\Twital\Helper\ParserHelper;


class ExpressionParserTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @dataProvider getData
     */
    public function testExpressions($expression, $splitter, $expected)
    {
        $splitted = ParserHelper::staticSplitExpression($expression, $splitter);
        $this->assertEquals($expected, $splitted);
    }
    /**
     * @dataProvider getWrongData
     * @expectedException Exception
     */
    public function testWrongExpressions($expression)
    {
        $splitted = ParserHelper::staticSplitExpression($expression, "xxx");
        $this->assertEquals($expected, $splitted);
    }
    public function getWrongData()
    {
        return array(

            array('a? "b'),
            array('a? \'b'),
            array('a? b)'),

        );
    }
    public function getData()
    {
        return array(
            array('?a b', '?', array('a b')),
            array('a b?', '?', array('a b')),
            array('a b', '?', array('a b')),

            array('a? b', '?', array('a', 'b')),
            array('a? "b?"', '?', array('a', '"b?"')),
            array('"a?"? "b?"', '?', array('"a?"', '"b?"')),
            array('"a?" ? "b?"', '?', array('"a?"', '"b?"')),
            array('"a?" ? "b?"', '?', array('"a?"', '"b?"')),

            array('\'a?\' ? "b?"', '?', array('\'a?\'', '"b?"')),

            //non quoted group
            array('(a?) ? "b?"', '?', array('(a?)', '"b?"')),
            array('"a?" ? (b?)', '?', array('"a?"', '(b?)')),

            // long splitter
            array('"a?" ?! "b?"', '?!', array('"a?"', '"b?"')),
            array('"a?" ?! "b?!"', '?!', array('"a?"', '"b?!"')),

        );
    }
}


