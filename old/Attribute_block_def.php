<?php
namespace goetas\atal\attribute;
use goetas\xml;
use Exception;
use goetas\atal\Attribute;
use goetas\atal\Compiler;
use goetas\atal\Twital;
class Attribute_block_def extends Attribute{
	protected $fatto = false;

	protected $elsToRemove = array();

	public function prependPI() {
		if(!$this->fatto){
			$this->compiler->getPostFilters()->addFilter( array($this, "_removeEls" ) );
		}
	}
	public function _removeEls($stream) {
		foreach ($this->elsToRemove as $el){
			$stream = preg_replace("~<([0-9a-z]+:)?".preg_quote($el, "~")."~", "", $stream);
			$stream = preg_replace("~</([0-9a-z]+:)?".preg_quote($el, "~").">~", "", $stream);
		}
		return $stream;
	}
	function start(xml\XMLDomElement $node, \DOMAttr $att){
		$this->prependPI();
		$piS = $this->dom->createProcessingInstruction( "php", "\nfunction {$att->value} (array \$__atal__scope, \$__atal__parent = 0) {\n\textract(\$__atal__scope); unset(\$__atal__scope);\n" );
		$piE = $this->dom->createProcessingInstruction( "php", " \n}\n " );


		$piSif = $this->dom->createProcessingInstruction( "php", "\nif (!\$__atal__parent){ // start self omit\n" );
		$piEif = $this->dom->createProcessingInstruction( "php", " \n} // end self omit\n " );

		$newNode = $node->ownerDocument->addChildNs(Twital::NS, "atal-block");


		$newNode->appendChild($piS);
		$newNode->addTextChild("\n");

		$nodeName = "atal-block-remove-".spl_object_hash($node);

		$this->elsToRemove[]=$nodeName;

		$newNode->appendChild($piSif);

		$tomittedNode = $newNode->addChildNs(Twital::NS, $nodeName);


		$tomittedNode->appendChild($piEif);

		$node->removeAttributeNode($att);

		$startToCopy = 0;
		foreach ( $node->attribute as $attNode ) {
			if($startToCopy){
				$tomittedNode->setAttributeNS ($attNode->namespaceURI, $attNode->name, $attNode->value);
			}elseif($attNode->namespaceURI==Twital::NS && $attNode->name=='block-call'){
				$startToCopy = 1;
			}
		}
		//$tomittedNode->setAttributeNS(Twital::NS, "omit", "\$__atal__parent");

		foreach ($node->childNodes as $nd){
			$tomittedNode->appendChild($nd->cloneNode(true));
		}

		$newNode->appendChild($piE);




		$this->compiler->applyTemplates($tomittedNode);


		if($att->value == "rowImmobileElenco"){
			//echo $newNode->saveXML();
			//die();
		}

	}
}

