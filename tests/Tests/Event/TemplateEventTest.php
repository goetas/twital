<?php
namespace Goetas\Twital\Tests\Event;

use Goetas\Twital\EventDispatcher\TemplateEvent;
use Goetas\Twital\Template;
use Goetas\Twital\Twital;
use PHPUnit\Framework\TestCase;

class TemplateEventTest extends TestCase
{
    private $twital;
    private $template;

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->twital = new Twital();
        $this->template = $this->getTemplateMock();
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

    protected function getTemplateMock()
    {
        return $this->getMockBuilder(Template::class)->setConstructorArgs(array(new \DOMDocument('1.0', 'UTF-8')))->getMock();
    }
}
