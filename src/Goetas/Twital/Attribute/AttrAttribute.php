<?php
namespace Goetas\Twital\Attribute;

use Goetas\Twital\Attribute;
use Goetas\Twital\CompilationContext;
use DOMAttr;
use Goetas\Twital\ParserHelper;
use Exception;

class AttrAttribute implements Attribute
{

    public static function getVarname(\DOMNode $node)
    {
        return "__a" . abs(crc32(spl_object_hash($node))) % 200;
    }

    public function visit(DOMAttr $att, CompilationContext $context)
    {
        $node = $att->ownerElement;
        $expressions = ParserHelper::staticSplitExpression($att->value, ",");
        $varName = self::getVarname($node);

        $parts = array();
        foreach ($expressions as $k => $expression) {
            $expressions[$k] = $attrExpr = self::splitAttrExpression($expression);
            if ($node->hasAttribute($attrExpr['name'])) {
                $attNode = $node->getAttributeNode($attrExpr['name']);

                if (preg_match('/{{.+}}/', $attNode->value)) {
                    throw new Exception("Non puoi usare t:attr su un attributo gia definito come variabile");
                } else {
                    $parts[$attrExpr['name']] = "['" . addcslashes($attNode->value, "'") . "']";
                }
                $node->removeAttributeNode($attNode);
            }
        }

        $code=array();
        $setAdd = $parts ?("|merge({" . ParserHelper::implodeKeyed(",", $parts) ."})"):"";
        $code[] = $context->createControlNode(
            "set $varName = $varName|default({})$setAdd"
        );
        foreach ($expressions as $attrExpr) {

            $nameParts = explode(":", $attrExpr['name']);
            $codeAttr = $context->getDocument()->createTextNode('');
            if (count($nameParts) == 2) {
                if ($node->lookupNamespaceURI($nameParts[0]) === null) {
                    throw new Exception(
                        "Preffisso '$nameParts[0]' non ha nessun namespace associato in '{" .
                             $node->namespaceURI . "}" . $node->nodeName . "'");
                } else {
                    $codeAttr = self::setExpression($varName, "xmlns:{$nameParts[0]}",
                        "['" . addcslashes($node->lookupNamespaceURI($nameParts[0]), "'") . "']");
                    $codeAttr = $context->createControlNode($codeAttr);
                }
            }


            if (isset($attrExpr['test']) && ($attrExpr['test'] == "true" || $attrExpr['test'] == "1")) {
                $code [] = $context->createControlNode("if {$attrExpr['test']}");
                $code [] = $codeAttr;
                $code [] = $context->createControlNode($this->getSetExpression($varName, $attrExpr['name'], $attrExpr['expr']));
                $code[] = $context->createControlNode("endif");
            } else {
                $code [] = $codeAttr;
                $code [] = $this->getSetExpression($varName, $attrExpr['name'], $attrExpr['expr']);;
            }
        }

        $node->setAttribute("__attr__", $varName);

        $ref = $node;
        foreach(array_reverse($code) as $line){
            $node->parentNode->insertBefore($line, $ref);
            $ref = $line;
        }


        $node->removeAttributeNode($att);
    }

    protected function getSetExpression($varName, $attName, $expr)
    {
        return self::setExpression($varName, $attName, $expr);
    }

    protected static function setExpression($varName, $attName, $expr)
    {
        return "set {$varName} = {$varName}|merge({ $attName:[{$expr}] })";
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
