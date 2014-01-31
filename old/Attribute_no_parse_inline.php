<?php
namespace goetas\atal\attribute;
use goetas\xml;
use goetas\atal\Twital;
use goetas\atal\Attribute;

class Attribute_no_parse_inline extends Attribute {

	public function init() {
		$this->compiler->addPostFilter( array(__CLASS__, "replaceInlines" ) );
	}
	function start(xml\XMLDomElement $node, \DOMAttr $att) {
		foreach ( $node->query( ".//text()|.//@*", array("t" => Twital::NS ) ) as $nodo ){
			if($nodo instanceof \DOMAttr){
				$nodo->value = preg_replace($this->compiler->currRegex,"__atal_inline\\1}atal_inline__", $nodo->value);
			}elseif ($nodo instanceof \DOMText){
				$nodo->data = preg_replace($this->compiler->currRegex,"__atal_inline\\1}atal_inline__", $nodo->data);
			}
		}
		return self::STOP_NODE;
	}
	public static function replaceInlines($stream) {
		return preg_replace( "/__atal_inline([^\\}]+)\\}atal_inline__/", "{\\1}", $stream );
	}

}