<?php
namespace goetas\atal\attribute;
use goetas\xml;
use goetas\atal\Attribute;
class Attribute_include extends Attribute{
	function start(xml\XMLDomElement $node, \DOMAttr $att){
		if($att->value[0]=="#"){
			$att->value = $this->compiler->getTemplate()->getRef().$att->value;
		}
		$pi = $this->dom->createProcessingInstruction("php",$this->generatePI($node,$att));
		$node->removeChilds();
		$node->appendChild($pi);
		return self::STOP_NODE;
	}

	protected function generatePI(xml\XMLDomElement $node, \DOMAttr $att) {
		$piStr = "\n".

		"\$__ntal = clone(\$this->getTal());\n".

		"\$__ntal->addScope(get_defined_vars());\n".
		"\$__ntal->xmlDeclaration = false;\n".
		"\$__ntal->dtdDeclaration = false;\n".

		"\$__ntal->outputTemplate(\$this->getTal()->convertTemplateName(\"".addcslashes($att->value,"\"")."\", \$this->getTemplateRef()));\n".

		"unset(\$__ntal,\$__tal_exception);\n";
		return $piStr;
	}
}
