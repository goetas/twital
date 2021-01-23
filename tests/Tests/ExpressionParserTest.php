<?php
namespace Goetas\Twital\Tests;

use Goetas\Twital\Helper\ParserHelper;
use PHPUnit\Framework\TestCase;

class ExpressionParserTest extends TestCase
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
     * @dataProvider getDataWithLimit
     */
    public function testExpressionsWithLimit($expression, $splitter, $limit, $expected)
    {
        $splitted = ParserHelper::staticSplitExpression($expression, $splitter, $limit);
        $this->assertEquals($expected, $splitted);
    }

    /**
     * @dataProvider getWrongData
     */
    public function testWrongExpressions($expression)
    {
        $this->expectException(\Exception::class);
        ParserHelper::staticSplitExpression($expression, "x");
    }

    public function getWrongData()
    {
        return array(

            array('a? "b'),
            array('a? \'b'),
            array('a? b)'),

        );
    }

    public function getDataWithLimit()
    {
        return array(
            array('a', '?', 2, array('a')),
            array('?', '?', 2, array('', '')),
            array('a?b?c', '?', 2, array('a', 'b?c')),
            array('a?(b?c)', '?', 2, array('a', '(b?c)')),
            array('a?(b?c)?d?e', '?', 3, array('a', '(b?c)', 'd?e')),
            array('[a?(b?c)?d]?e', '?', 3, array('[a?(b?c)?d]', 'e')),

            array('[aa?(bb?cc)?dd]?ee', '?', 3, array('[aa?(bb?cc)?dd]', 'ee')),
            array('[aa?"bb?cc"?dd]?ee', '?', 3, array('[aa?"bb?cc"?dd]', 'ee')),

            array('[aa?"bb\"?cc"?dd]?ee', '?', 3, array('[aa?"bb\"?cc"?dd]', 'ee')),
        );
    }

    public function getData()
    {
        return array(
            array('?a b', '?', array('', 'a b')),

            array('a b?', '?', array('a b', '')),

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

            array("aaa:{'aaa':'xxx', 'bbb':'cc'},title", ',', array("aaa:{'aaa':'xxx', 'bbb':'cc'}", "title")),
            array("'aaa':'xxx', 'bbb':'cc'", ',', array("'aaa':'xxx'", "'bbb':'cc'")),
        );
    }
}
