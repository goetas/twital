<?php
namespace Goetas\Twital\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Goetas\Twital\EventDispatcher\SourceEvent;


class CustomNamespaceRawSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(){
    	return array(
    		'compiler.pre_load'=>'addCustomNamespace'
    	);
    }
    /**
     *
     * @var array
     */
    protected $customNamespaces=array();

    public function __construct(array $customNamespaces)
    {
        $this->customNamespaces = $customNamespaces;
    }

    public function addCustomNamespace(SourceEvent $event)
    {
        $xml = trim($event->getTemplate());

        foreach($this->customNamespaces as $prefix => $ns){
            if(!preg_match('/ xmlns:([a-z0-9\-]+)=("|\')' . preg_quote($ns, '/') . '("|\')/', $xml)){

                $start = strpos($xml, '<?')!==0?(strpos($xml, '?>')+2):0;

                $addPos = strpos($xml, '>', $start);
                if($xml[$addPos-1]=="/"){
                    $addPos--;
                }
                $xml = substr_replace($xml, ' xmlns:' . $prefix . '="' . $ns . '"', $addPos,0);
            }
        }

        $event->setTemplate($xml);
    }
}