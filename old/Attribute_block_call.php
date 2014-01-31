<?php
namespace goetas\atal\attribute;
use goetas\xml;
use goetas\atal\Twital;
use Exception;
use goetas\atal\Attribute;
use goetas\atal\Compiler;
class Attribute_block_call extends Attribute{
	protected $fatto = false;

	protected $elsToRemove = array();

	public function prependPI() {
		if(!$this->fatto){
			$this->compiler->getPostFilters()->addFilter( array($this, "_removeEls" ) );
		}
	}
	public function _removeEls($stream) {
		foreach ($this->elsToRemove as $el){
			$stream = preg_replace("~(><([0-9a-z]+:)?".preg_quote($el, "~")."/>)|(><([0-9a-z]+:)?".preg_quote($el, "~")."></([0-9a-z]+:)?".preg_quote($el, "~").">)~", "", $stream);
		}
		return $stream;
	}
	function start(xml\XMLDomElement $node, \DOMAttr $att){
		$this->prependPI();
		$pi = $this->dom->createProcessingInstruction("php",self::prepareCode($att, $this->compiler));
		$node->removeChilds();


		$nodeName = "atal-block-marker-".spl_object_hash($node);

		$this->elsToRemove[]=$nodeName;

		$node->addChildNs(Twital::NS, $nodeName);

		$node->appendChild($pi);



		$attrs = array();
		foreach ( $node->attribute as $attNode ) {
			$attrs[]=$attNode;
		}
		$startToRemove = 0;
		foreach ( $attrs as $attNode ) {
			if($startToRemove){
				$node->removeAttributeNode($attNode);
			}elseif($attNode===$att){
				$startToRemove = 1;
			}
		}
		return self::STOP_NODE | self::STOP_ATTRIBUTE;
	}
	public static function prepareCode(\DOMAttr $att, Compiler $compiler, $scope = '$this->', $parent = 0){

		$expressions = $compiler->splitExpression($att->value,";");

		$functname = array_shift($expressions);

		if(count($expressions)){
			$code="call_user_func(function(\$__atal__scope){\n\t";
			$code.="extract(\$__atal__scope);unset(\$__atal__scope);\n";
			foreach ($expressions as $expression){
				$mch=array();
				if(preg_match("/^(".preg_quote( '$', "/" ) . "[a-zA-Z_\\x7f-\\xff][a-zA-Z0-9_\\x7f-\\xff]*)([^=]*)\\s*=\\s*(.+)/", $expression, $mch)){
					$code.="$mch[1]$mch[2] = $mch[3];\n";
				}else{
					throw new Exception("Sintassi plugin non valida: '$att->value'");
				}
			}
			$code .="\$ret = get_defined_vars(); unset(\$ret['__atal__scope']);\n";
			$code .="return \$ret;\n}, get_defined_vars())\n";
		}else{
			$code="get_defined_vars()";
		}
		$fcode .= "unset(\$__atal__parent); {$scope}{$functname}($code, $parent); ";

		return $fcode;
	}

}
