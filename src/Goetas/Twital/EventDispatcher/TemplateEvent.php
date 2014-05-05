<?php
namespace Goetas\Twital\EventDispatcher;

use Symfony\Component\EventDispatcher\Event;
use Goetas\Twital\Twital;
use Goetas\Twital\Template;

/**
 *
 * @author Asmir Mustafic <goetas@gmail.com>
 *
 */
class TemplateEvent extends Event
{
    /**
     *
     * @var Twital
     */
    protected $twital;
    /**
     *
     * @var Template
     */
    protected $template;
    public function __construct(Twital $twital, Template $template)
    {
    	$this->twital = $twital;
    	$this->template = $template;
    }
    /**
     * @return \Goetas\Twital\Twital
     */
	public function getTwital() {
		return $this->twital;
	}
	/**
	 * @return \Goetas\Twital\Template
	 */
	public function getTemplate() {
		return $this->template;
	}

	/**
	 * @param \Goetas\Twital\Template $template
	 * @return void
	 */
	public function setTemplate(Template $template) {
		$this->template = $template;
	}

}
