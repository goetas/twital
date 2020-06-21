<?php
namespace Goetas\Twital\EventDispatcher;

use Goetas\Twital\Template;
use Goetas\Twital\Twital;

/**
 *
 * @author Asmir Mustafic <goetas@gmail.com>
 *
 */
class TemplateEvent extends AbstractEvent
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
    public function getTwital()
    {
        return $this->twital;
    }

    /**
     * @return \Goetas\Twital\Template
     */
    public function getTemplate()
    {
        return $this->template;
    }

    /**
     * @param \Goetas\Twital\Template $template
     * @return void
     */
    public function setTemplate(Template $template)
    {
        $this->template = $template;
    }
}
