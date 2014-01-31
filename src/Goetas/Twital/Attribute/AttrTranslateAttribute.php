<?php
namespace Goetas\Twital\Attribute;

use Goetas\Twital\Attribute;
use Goetas\Twital\TwitalLoader;
use DOMAttr;
use Goetas\Twital\ParserHelper;

class AttrTranslateAttribute implements Attribute
{

    public static function getVarname(\DOMNode $node)
    {
        return "__a" . abs(crc32(spl_object_hash($node))) % 200;
    }

    function visit(DOMAttr $att, TwitalLoader $twital)
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

        $code = "{% set $varName = $varName|default({})|merge({" . ParserHelper::implodeKeyed(",", $parts) . "} %})";
        $node->setAttribute("__attr__", $varName);

        $pi = $node->ownerDocument->createTextNode($code);

        $node->parentNode->insertBefore($pi, $node);

        $node->removeAttributeNode($att);
    }
}