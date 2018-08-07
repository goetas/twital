<?php
namespace Goetas\Twital\Tests\Event;

use Goetas\Twital\EventDispatcher\SourceEvent;
use Goetas\Twital\Twital;

class SourceEventTest extends \PHPUnit_Framework_TestCase
{
    private $twital;

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        parent::setUp();
        $this->twital = new Twital();
    }

    public function testBase()
    {
        $template = md5(microtime());
        $ist = new SourceEvent($this->twital, $template);

        $this->assertSame($this->twital, $ist->getTwital());
        $this->assertSame($template, $ist->getTemplate());
    }

    public function testBaseSetter()
    {
        $template = md5(microtime());
        $ist = new SourceEvent($this->twital, $template);

        $this->assertSame($template, $ist->getTemplate());

        $templateNew = md5(microtime());
        $ist->setTemplate($templateNew);

        $this->assertSame($templateNew, $ist->getTemplate());
    }
}
