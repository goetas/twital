<?php
namespace Goetas\Twital\Extension;

use Goetas\Twital\EventSubscriber\FixTwigExpressionSubscriber;

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
        );
    }
}
