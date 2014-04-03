<?php
namespace Goetas\Twital\Attribute;

use Goetas\Twital\Compiler;
use Goetas\Twital\Helper\ParserHelper;

/**
 *
 * @author Asmir Mustafic <goetas@gmail.com>
 *
 */
class AttrAppendAttribute extends AttrAttribute
{

    public function visit(\DOMAttr $att, Compiler $context)
    {
        $node = $att->ownerElement;
        $expressions = ParserHelper::staticSplitExpression($att->value, ",");

        $attributes = array();
        foreach ($expressions as $k => $expression) {
            $expressions[$k] = $attrExpr = self::splitAttrExpression($expression);
            $attNode = null;
            if (! isset($attributes[$attrExpr['name']])) {
                $attributes[$attrExpr['name']] = array();
            }
            if ($node->hasAttribute($attrExpr['name'])) {
                $attNode = $node->getAttributeNode($attrExpr['name']);
                $node->removeAttributeNode($attNode);
                $attributes[$attrExpr['name']][] = "'" . addcslashes($attNode->value, "'") . "'";
            }
            if ($attrExpr['test'] === "true" || $attrExpr['test'] === "1") {
                unset($expressions[$k]);
                $attributes[$attrExpr['name']][] = $attrExpr['expr'];
            }
        }

        $code = array();

        $varName = self::getVarname($node);
        $code[] = $context->createControlNode("if $varName is not defined");
        $code[] = $context->createControlNode("set $varName = {" . ParserHelper::implodeKeyedDouble(",", $attributes) . " }");
        $code[] = $context->createControlNode("else");
        $code[] = $context->createControlNode("set $varName = $varName|merge({" . ParserHelper::implodeKeyedDouble(",", $attributes) . "})");
        $code[] = $context->createControlNode("endif");

        foreach ($expressions as $attrExpr) {
            $code[] = $context->createControlNode("if {$attrExpr['test']}");
            $code[] = $context->createControlNode(
                "set {$varName} = {$varName}|merge({ '{$attrExpr['name']}':{$varName}.{$attrExpr['name']}|merge([{$attrExpr['expr']}]) })");
            $code[] = $context->createControlNode("endif");
        }

        $this->addSpecialAttr($node, $varName, $code);
        $node->removeAttributeNode($att);
    }
}
