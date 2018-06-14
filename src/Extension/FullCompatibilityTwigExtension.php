<?php
namespace Goetas\Twital\Extension;

use Goetas\Twital\EventSubscriber\CustomNamespaceSubscriber;
use Goetas\Twital\EventSubscriber\FixTwigExpressionSubscriber;
use Goetas\Twital\Twital;

/**
 *
 * @author Martin Hasoň <martin.hason@gmail.com>
 *
 */
class FullCompatibilityTwigExtension extends AbstractExtension
{
    public function getSubscribers()
    {
        return array(
            new FixTwigExpressionSubscriber(),
            new CustomNamespaceSubscriber(array(
                't' => Twital::NS
            )),
        );
    }
}
