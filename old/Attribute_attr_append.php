<?php
namespace goetas\atal\attribute;
use goetas\xml;
use goetas\atal\Attribute;
use Exception;
class Attribute_attr_append extends Attribute_attr {

	function start(xml\XMLDomElement $node, \DOMAttr $att) {
		$this->prependPI($node);
		$expressions = $this->compiler->splitExpression( $att->value, ";" );

		$varName = "\$__attr_" . spl_object_hash($node);
		$precode =  "if(!isset($varName)){ $varName=array(); }\n";
		$code = '';
		$regex = "/" . preg_quote( "[#tal_attr#", "/" ) . "(" . preg_quote( '$', "/" ) . "[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)" . preg_quote( "#tal_attr#]", "/" ) . "/";

		foreach ( $expressions as $expression ){
			list ( $condition, $attName, $attExpr ) = $this->splitAttrExpression( $expression );
			
			
			if($node->hasAttribute( $attName ) && !isset($this->attrsToRemove  [spl_object_hash($node).$attName])){
				
				$attVal = $node->getAttribute( $attName );

				if(preg_match( $regex, $attVal )){
					$precode .= $varName . "['$attName'][]=\"" . addcslashes(preg_replace( $regex, "{\\1}", $attVal ),"\"\\") . "\";\n";
				}else{
					$precode .= $varName . "['$attName'][]='" . addcslashes( $node->getAttribute( $attName ), "'" ) . "';\n";
				}
				$this->attrsToRemove  [spl_object_hash($node).$attName] = array($node, $attName );
				
			}
			

			list ( $prefix, $name ) = explode( ":", $attName );

			if(strlen( $prefix ) && strlen( $name )){
				if($node->lookupNamespaceURI( $prefix ) === null){
					throw new Exception( "Preffisso '$prefix' non ha nessun namespace associato in '{" . $node->namespaceURI . "}" . $node->nodeName . "'" );
				}else{
					$code .= $varName . "['xmlns:$prefix']='" . addcslashes($node->lookupNamespaceURI( $prefix ),"'")  . "'; \n";
				}
			}
			$code .= "if ($condition) { " . $varName . "['$attName'][]=" . $this->compiler->parsedExpression( $attExpr ) . "; }\n";
		}
		
		$pi = $this->dom->createProcessingInstruction( "php", $precode . $code );
		$node->parentNode->insertBefore( $pi, $node );
		$node->setAttribute( "atal-attr", "__atal-attr($varName)" );
	}
}
