<?php
namespace Goetas\Twital\Attribute;

use Goetas\Twital\Attribute as AttributeBase;
use Goetas\Twital\Compiler;
use Goetas\Twital\Helper\DOMHelper;
use Goetas\Twital\Twital;

/**
 * @author Asmir Mustafic <goetas@gmail.com>
 */
class ExtendsAttribute implements AttributeBase
{
    public function visit(\DOMAttr $att, Compiler $context)
    {
        $node = $att->ownerElement;

        $filename = '"' . $att->value . '"';


        $xp = new \DOMXPath($att->ownerDocument);
        $xp->registerNamespace("t", Twital::NS);

        $candidates = array();
        foreach ($xp->query(".//*[@t:block-inner or @t:block-outer]|.//t:*", $node) as $blockNode) {

            $ancestors = $xp->query("ancestor::*[@t:block-inner or @t:block-outer or @t:extends]", $blockNode);

            if ($ancestors->length === 1) {
                $candidates[] = $blockNode;
            }
        }

        foreach ($candidates as $candidate) {
            if ($candidate->parentNode !== $node) {
                $candidate->parentNode->removeChild($candidate);
                $node->appendChild($candidate);
            }
        }
        if ($candidates) {
            foreach (iterator_to_array($node->childNodes) as $k => $item) {
                if (!in_array($item, $candidates, true)) {
                    $node->removeChild($item);
                }
            }
        }

        $context->compileChilds($node);

        $set = iterator_to_array($node->childNodes);
        if (count($set)) {
            $n = $node->ownerDocument->createTextNode("\n");
            array_unshift($set, $n);
        }
        $ext = $context->createControlNode("extends {$filename}");
        array_unshift($set, $ext);

        DOMHelper::replaceWithSet($node, $set);
    }
}
