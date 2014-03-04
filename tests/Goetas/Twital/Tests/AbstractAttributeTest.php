<?php
namespace Goetas\Twital\Tests;
use Goetas\Twital\TwitalLoader;
/**
 * Attribute test case.
 */
abstract class AbstractAttributeTest extends \PHPUnit_Framework_TestCase
{
    /**
     *
     * @var \DOMDocument
     */
    protected $dom;
    /**
     *
     * @var OmitAttribute
     */
    protected $attribute;
    protected abstract function getAttribute();
    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        parent::setUp();
        $this->attribute = $this->getAttribute();
        $this->dom = new \DOMDocument('1.0', 'UTF-8');
        $this->dom->loadXML('<div>content</div>');
    }

    protected abstract function getData();
    protected abstract function getTagName();
    /**
     * @dataprovider getData
     */
    public function testVisit($source, $expected)
    {

        $this->dom->documentElement->setAttributeNs(TwitalLoader::NS,$this->getTagName(), $source);

        $this->attribute->visit($this->dom, $this->context);

        $this->assertEquals($expected, $this->dom->saveXML($this->dom));
    }
}

