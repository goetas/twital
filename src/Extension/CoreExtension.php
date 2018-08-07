<?php
namespace Goetas\Twital\Extension;

use Goetas\Twital\Attribute;
use Goetas\Twital\EventSubscriber\ContextAwareEscapingSubscriber;
use Goetas\Twital\EventSubscriber\CustomNamespaceRawSubscriber;
use Goetas\Twital\EventSubscriber\DOMMessSubscriber;
use Goetas\Twital\EventSubscriber\FixHtmlEntitiesInExpressionSubscriber;
use Goetas\Twital\EventSubscriber\IDNodeSubscriber;
use Goetas\Twital\Node;
use Goetas\Twital\Twital;

/**
 *
 * @author Asmir Mustafic <goetas@gmail.com>
 *
 */
class CoreExtension extends AbstractExtension
{
    public function getSubscribers()
    {
        return array(
            new DOMMessSubscriber(),
            new CustomNamespaceRawSubscriber(array(
                't' => Twital::NS
            )),
            new FixHtmlEntitiesInExpressionSubscriber(),
            new ContextAwareEscapingSubscriber(),
            new IDNodeSubscriber()
        );
    }

    public function getAttributes()
    {
        $attributes = array();
        $attributes[Twital::NS]['__base__'] = new Attribute\BaseAttribute();
        $attributes[Twital::NS]['__internal-id__'] = new Attribute\InternalIDAttribute();

        $attributes[Twital::NS]['if'] = new Attribute\IfAttribute();
        $attributes[Twital::NS]['elseif'] = new Attribute\ElseIfAttribute();
        $attributes[Twital::NS]['else'] = new Attribute\ElseAttribute();

        $attributes[Twital::NS]['omit'] = new Attribute\OmitAttribute();
        $attributes[Twital::NS]['set'] = new Attribute\SetAttribute();

        $attributes[Twital::NS]['content'] = new Attribute\ContentAttribute();
        $attributes[Twital::NS]['capture'] = new Attribute\CaptureAttribute();
        $attributes[Twital::NS]['replace'] = new Attribute\ReplaceAttribute();

        $attributes[Twital::NS]['attr'] = new Attribute\AttrAttribute();
        $attributes[Twital::NS]['attr-append'] = new Attribute\AttrAppendAttribute();

        $attributes[Twital::NS]['extends'] = new Attribute\ExtendsAttribute();

        $attributes[Twital::NS]['block'] = new Attribute\BlockInnerAttribute();
        $attributes[Twital::NS]['block-inner'] = new Attribute\BlockInnerAttribute();
        $attributes[Twital::NS]['block-outer'] = new Attribute\BlockOuterAttribute();

        return $attributes;
    }

    public function getNodes()
    {
        $nodes = array();
        $nodes[Twital::NS]['extends'] = new Node\ExtendsNode();
        $nodes[Twital::NS]['block'] = new Node\BlockNode();
        $nodes[Twital::NS]['macro'] = new Node\MacroNode();
        $nodes[Twital::NS]['import'] = new Node\ImportNode();
        $nodes[Twital::NS]['include'] = new Node\IncludeNode();
        $nodes[Twital::NS]['omit'] = new Node\OmitNode();
        $nodes[Twital::NS]['embed'] = new Node\EmbedNode();
        $nodes[Twital::NS]['use'] = new Node\UseNode();

        return $nodes;
    }
}
