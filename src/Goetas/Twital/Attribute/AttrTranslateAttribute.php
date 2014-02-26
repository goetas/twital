<?php
namespace Goetas\Twital\Attribute;

use Goetas\Twital\Attribute;
use Goetas\Twital\CompilationContext;
use DOMAttr;
use Goetas\Twital\ParserHelper;

class AttrTranslateAttribute implements Attribute
{

    public static function getVarname(\DOMNode $node)
    {
        return "__a" . abs(crc32(spl_object_hash($node))) % 200;
    }

    public function visit(DOMAttr $att, CompilationContext $context)
    {
        $node = $att->ownerElement;
        $expressions = ParserHelper::staticSplitExpression($att->value, ";");
        $varName = self::getVarname($node);

        $parts = array();

        foreach ($expressions as $expression) {

            $attrExpr = ParserHelper::staticSplitExpression($expression, "=");

            if (! $node->hasAttribute($attrExpr[0])) {
                throw new Exception("non trovo l'attributo " . $attrExpr[0] . " da tradurre");
            }

            $attNode = $node->getAttributeNode($attrExpr[0]);

            $parts[$attrExpr[0]] = "['" . addcslashes($attNode->value, "'") . "'|translate" . (isset($attrExpr[1]) ? $attrExpr[1] : '') . "]";

            $node->removeAttributeNode($attNode);
        }

        $code = "set $varName = $varName|default({})|merge({" . ParserHelper::implodeKeyed(",", $parts) . "})";
        $node->setAttribute("__attr__", $varName);

        $pi = $context->createControlNode($code);

        $node->parentNode->insertBefore($pi, $node);

        $node->removeAttributeNode($att);
    }
}
