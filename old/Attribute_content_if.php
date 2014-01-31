<?php
namespace goetas\atal\attribute;
use goetas\xml;
use goetas\atal\Attribute;
use goetas\atal\Compiler;
use goetas\atal\Exception;
class Attribute_content_if extends Attribute{
	function start(xml\XMLDomElement $node, \DOMAttr $att){
		$attValue = $att->value;
		if(strlen(trim($attValue))>0){
			$expressions = $this->compiler->splitExpression( $attValue, ";" );

			$code = "if (0){";
			foreach ( $expressions as $expression ){
				list ( $condition, $valueExpr ) = self::splitAttrExpression($this->compiler,  $expression );

				$code .= "} elseif ($condition) {\n";
				$code .= "print(" . $this->compiler->parsedExpression( $valueExpr ) . "); \n";
			}
			$nsp = " xmlns=\"".$node->lookupNamespaceURI(null)."\"";
			$str = str_replace($nsp,"", trim( $node->saveXML( false )) ) ;
			$code .= "} else { print( '" . addcslashes( $str , "\\'" ) . "' ); }\n";

			$pi = $this->dom->createProcessingInstruction("php", $code);

			$node->removeChilds();
			$node->appendChild($pi);
		}else{
			$node->removeChilds();
		}
		return self::STOP_NODE;
	}
	protected static function splitAttrExpression(Compiler $compiler, $str) {
		$parts = $compiler->splitExpression( $str, "?" );
		if(count( $parts ) == 1){
			return array('true', $parts [0] );
		}elseif(count( $parts ) == 2){
			return array($parts [0], $parts [1] );
		}else{
			throw new Exception( __METHOD__ . " error in '$str'" );
		}
	}
}