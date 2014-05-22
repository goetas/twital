<?php
namespace Goetas\Twital\Tests\Event;

use Goetas\Twital\Twital;
use Goetas\Twital\EventDispatcher\TemplateEvent;

class TemplateEventTest extends \PHPUnit_Framework_TestCase
{
    private $twital;
    private $template;

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        parent::setUp();
        $this->twital = new Twital();
        $this->template = $this->getTemplateMock();
    }

    protected function getTemplateMock()
    {
        return $this->getMock('Goetas\Twital\Template', null, array(new \DOMDocument('1.0', 'UTF-8')));
    }

    public function testBase()
    {
        $ist = new TemplateEvent($this->twital, $this->template);

        $this->assertSame($this->twital, $ist->getTwital());
        $this->assertSame($this->template, $ist->getTemplate());
    }

    public function testBaseSetter()
    {
        $ist = new TemplateEvent($this->twital, $this->template);

        $template = $this->getTemplateMock();
        $ist->setTemplate($template);

        $this->assertSame($template, $ist->getTemplate());
    }
}
