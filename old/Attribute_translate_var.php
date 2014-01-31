<?php
/**
 * Definisce delle variabili per l Twital
 * ES:
 * &lt;img xmlns:t="Twital" t:translate-var="$var='Estivi'" title="eventi %tipo" t:translate-attr="title(tipo=$var)"/&gt;
 * &lt;p xmlns:t="Twital" t:translate-var="$var='Eventi del %anno'" t:content="$var|translate-var:anno='2009'"&gt;testo di prova&lt;/p&gt;
 */
namespace goetas\atal\attribute;
use goetas\xml;
use goetas\atal\Exception;
use goetas\atal\Attribute;
use goetas\atal\Twital;

class Attribute_translate_var extends Attribute {
	function start(xml\XMLDomElement $node, \DOMAttr $att) {
		
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
		
		
		$options = array();
		
		
		foreach ( $this->compiler->splitExpression( $att->value, ";" ) as $part ){
			$partsTr = $this->compiler->splitExpression( $part, "|" );
			
			
			list ( $varName, $text ) = $this->compiler->splitExpression( $partsTr[0], "=" );
			
			if($varName[0]!='$'){
				throw new Exception( "errore di sintassi vicino a '" . $varName . "'" );
			}
			$first = $text[0];
			$last = $text[strlen($text)-1];
			if(! (($first=="\""  &&  $last== "\"" ) || ( $first== "'"  &&  $last== "'" ))){
				throw new Exception( "Errore di sintassi vicino a '" . $text . "'" );
			}
			if($partsTr[1]){
				$parts = $this->compiler->splitExpression( $partsTr[1], "|" );
			}else{
				$parts = array();
			}
			$modParams = array();
			foreach ( $parts as $part ) {
				if (preg_match ( '#(^[a-z][a-z0-9_\\-]*\s*:)#i', $part, $mch )) { // modificatore con parametri
					// modifier con parametri
					$modifierParts = $this->compiler->splitExpression ( $part, ':' );
					$modName = array_shift ( $modifierParts );
					
					foreach ( $modifierParts as $modifierParam ) {
						$mch = array ();
						if (preg_match ( "/^([a-z][a-z0-9_\\-]*)\\s*\\=(.*)/i", $modifierParam, $mch )) { // parametri con nome
							$exs = trim ( $mch [2] );
							$exs = $exs[0] == "(" && $exs [strlen ( $exs ) - 1] == ")" ? substr ( $exs, 1, - 1 ) : $exs;
							$modParams[$mch [1]]=$this->compiler->parsedExpression ( $exs, true );
						} else { // parametri numerici
							$paramStr = trim ( $modifierParam );
							$paramStr = $paramStr [0] == "(" && $paramStr [strlen ( $paramStr ) - 1] == ")" ? substr ( $paramStr, 1, - 1 ) : $paramStr;
							$modParams[] = $this->compiler->parsedExpression ( $paramStr, true );
						}
					}
				}elseif ( $part==='' || preg_match ( '#(^[a-z][a-z0-9_\\-]*$)#i', $part )) { // modificatore senza parametri o di default
					//
				} else{
					throw new Exception ( "Errore di sintassi vicino a '$part'" );
				}
				break;				
			}
		
			$expr =" \\".__CLASS__."::checkHtml(\$this->getTal()->getServices()->service('goetas\\\\atal\\\\plugins\\\\services\\\\translate\\\\ITranslate')->translate(" . $text . " , " . $this->compiler->dumpKeyed( $modParams ) . "  , $domain , array() ))";

			$code .= "$varName = " . $expr . ";\n";
		}

		$pi = $this->dom->createProcessingInstruction( "php", $code );
		$node->parentNode->insertBefore( $pi, $node );
	}
	public static function checkHtml($s) {
		if(strpos($s,"&")!==false){
			return preg_replace("/&(?![a-z]+;)/i","&amp;", $s);  // in caso che i traduttori sbaglino, sistemo le "&" con la relativa entita html
		}else{
			return $s;
		}
	}
}