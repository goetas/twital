<?php
namespace goetas\atal\attribute;
use goetas\xml\XMLDomElement;

use goetas\xml;
use goetas\atal\Attribute;
use goetas\atal\Twital;
class Attribute_translate extends Attribute {
	function start(xml\XMLDomElement $node, \DOMAttr $att) {
		$examine = $node;
		$domain = "null";
		do{
			if(is_array($examine->attribute)){
				foreach ($examine->attribute as $attr) {
					if($attr->namespaceURI == Twital::NS && $attr->locanName == 'translate-domain'){
						$domain = "'".addcslashes($attr->value,"\\'")."'";
						break 2;
					}
				}
			}
			$examine = $examine->parentNode;
		}while($examine);


		$parts = $this->compiler->splitExpression( $att->value, ";" );
		$params = array();
		$options = array();
		foreach ( $parts as $part ){
			list ( $k, $v ) = $this->compiler->splitExpression( $part, "=" );
			if($k[0]==":"){
				if(is_null($v)){
					$v = true;
				}
				$options[substr($k,1)]=$v;
			}else{
				if($k[0]==="'"){
					$k = substr($k, 1, -1);
				}else{
					$k = "%{$k}";
				}

				$params [$k] = $this->compiler->parsedExpression( $v , true);
			}
		}

		ksort($params);

		foreach ( $node->query( ".//*[@t:id]/@t:id", array("t" => Twital::NS ) ) as $tt ){
			$tt->ownerElement->removeAttributeNode( $tt );
		}

		$str = self::extractStringFromNode($node, $att);


		$code ="";
		if($options["nl2br"]){
			$code .=" nl2br( ";
		}
		$code .=" \\".__CLASS__."::checkHtml(\$this->getTal()->getServices()->service('goetas\\\\atal\\\\plugins\\\\services\\\\translate\\\\ITranslate')->translate('" . addcslashes( $str , "\\'" ) . "', " . $this->compiler->dumpKeyed( $params ) . "  , $domain , " . var_export( $options,1 ) . " ))";

		if($options["nl2br"]){
			$code .=" ) ";
		}
		$pi = $this->dom->createProcessingInstruction( "php", "print( $code );");

		$node->removeChilds();
		$node->appendChild( $pi );
		return self::STOP_NODE;
	}
	public static function extractStringFromNode(XMLDomElement $node, $attr = null) {
		if($node->prefix){
			$nsp = " xmlns:".$node->prefix."=\"".$node->lookupNamespaceURI($node->prefix)."\"";
		}else{
			$nsp = " xmlns=\"".$node->lookupNamespaceURI(null)."\"";
		}
		$str = str_replace($nsp,"", trim( $node->saveXML( false )) ) ;


		if(!class_exists('ambient\mvc\Ambiente', false) && $attr && strpos($attr->value, ':whitespace=true')===false && strpos($attr->value, ':whitespace = true')===false){
			$str = preg_replace('/\s+/', " ", $str);
		}
		return $str;
	}
	public static function checkHtml($s) {
		if(strpos($s,"&")!==false){
			return preg_replace("/&(?![a-z]+;)/i","&amp;", $s);  // in caso che i traduttori sbaglino, sistemo le "&" con la relativa entita html
		}else{
			return $s;
		}
	}
	public static function soloPrimitivi($v){
		if(is_scalar($v) || !$v){
			return true;
		}
		if(is_object($v) && method_exists( $v  , "__toString" )){
			return true;
		}
		return false;
	}
}