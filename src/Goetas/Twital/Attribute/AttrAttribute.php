<?php
namespace Goetas\Twital\Attribute;

use Goetas\Twital\Attribute;
use Goetas\Twital\TwitalLoader;
use DOMAttr;
use Goetas\Twital\ParserHelper;

class AttrAttribute implements Attribute
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
        foreach ($expressions as $k => $expression) {
            $expressions[$k] = $attrExpr = self::splitAttrExpression($expression);
            if ($node->hasAttribute($attrExpr['name'])) {
                $attNode = $node->getAttributeNode($attrExpr['name']);

                if (preg_match('/{{.+}}/', $attNode->value)) {
                    throw new Exception("Non puoi usarare t:attr su un attributo gia definito come variabile");
                } else {
                    $parts[$attrExpr['name']] = "['" . addcslashes($attNode->value, "'") . "']";
                }
                $node->removeAttributeNode($attNode);
            }
        }

        $code = "{% set $varName = $varName|default({})|merge({" . ParserHelper::implodeKeyed(",", $parts) . "}) %}\n";

        foreach ($expressions as $attrExpr) {

            $nameParts = explode(":", $attrExpr['name']);
            $codeAttr = '';
            if (count($nameParts) == 2) {
                if ($node->lookupNamespaceURI($nameParts[0]) === null) {
                    throw new Exception("Preffisso '$nameParts[0]' non ha nessun namespace associato in '{" . $node->namespaceURI . "}" . $node->nodeName . "'");
                } else {
                    $codeAttr = self::setExpression($varName, "xmlns:{$nameParts[0]}", "['" . addcslashes($node->lookupNamespaceURI($nameParts[0]), "'") . "']");
                }
            }

            $attCode = $codeAttr . $this->getSetExpression($varName, $attrExpr['name'], $attrExpr['expr']);
            if (isset($attrExpr['test']) && ($attrExpr['test'] == "true" || $attrExpr['test'] == "1")) {
                $code .= "{% if {$attrExpr['test']} %}" . $attCode . "{% endif %}\n";
            } else {
                $code .= $attCode;
            }
        }

        $node->setAttribute("__attr__", $varName);

        $pi = $node->ownerDocument->createTextNode($code);

        $node->parentNode->insertBefore($pi, $node);
        $node->removeAttributeNode($att);
    }

    protected function getSetExpression($varName, $attName, $expr)
    {
        return self::setExpression($varName, $attName, $expr);
    }

    protected static function setExpression($varName, $attName, $expr)
    {
        return "{% set {$varName}.{$attName} = [{$expr}]; %}";
    }

    public static function splitAttrExpression($str)
    {
        $parts = ParserHelper::staticSplitExpression($str, "?");
        if (count($parts) == 1) {
            $attr = self::findAttrParts($parts[0]);
            $attr['test'] = 'true';
            return $attr;
        } elseif (count($parts) == 2) {
            $attr = self::findAttrParts($parts[1]);
            $attr['test'] = $parts[0];
            return $attr;
        } else {
            throw new Exception(__CLASS__ . "::splitAttrExpression error in '$str'");
        }
    }

    protected static function findAttrParts($str)
    {
        $mch = array();
        if (preg_match("/^([a-z_][a-z0-9\\-_]*:[a-z][a-z0-9\\-_]*)\\s*=\\s*/i", $str, $mch)) {
            return array(
                'name' => $mch[1],
                'expr' => trim(substr($str, strlen($mch[0])))
            );
        } elseif (preg_match("/^([a-z_][a-z0-9\\-_]*)\\s*=\\s*/i", $str, $mch)) {
            return array(
                'name' => $mch[1],
                'expr' => trim(substr($str, strlen($mch[0])))
            );
        } else {
            throw new Exception(__CLASS__ . "::findAttrParts error in '$str'");
        }
    }
}