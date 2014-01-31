<?php
namespace goetas\atal\attribute;
use goetas\xml;
use goetas\atal\Attribute;
class Attribute_no_conflicts extends Attribute{

	protected $oldRegex = array();
	function start(xml\XMLDomElement $node, \DOMAttr $att){

		list($p1, $p2) = explode("|", $att->value);


		array_push($this->oldRegex , $this->compiler->getCurrRegex());

		$this->compiler->setCurrRegex("/".preg_quote($p1, "/").'([\'a-z\$\\\\].*?)'.preg_quote($p2, "/")."/");



	}
	public function end(xml\XMLDomElement $node, \DOMAttr $att) {
		$this->compiler->setCurrRegex(array_pop( $this->oldRegex ) );
	}

}