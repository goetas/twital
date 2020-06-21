<?php
namespace Goetas\Twital\EventDispatcher;

use Goetas\Twital\Twital;

/**
 *
 * @author Asmir Mustafic <goetas@gmail.com>
 *
 */
class SourceEvent extends AbstractEvent
{
    /**
     *
     * @var Twital
     */
    protected $twital;
    /**
     *
     * @var string
     */
    protected $template;

    public function __construct(Twital $twital, $template)
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
     * @return string
     */
    public function getTemplate()
    {
        return $this->template;
    }

    /**
     * @param string $template
     * @return void
     */
    public function setTemplate($template)
    {
        $this->template = $template;
    }
}
