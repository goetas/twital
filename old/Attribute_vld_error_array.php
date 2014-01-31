<?php
namespace goetas\atal\attribute;
use goetas\atal\DynamicAttribute;
use Traversable;
use Exception;
use goetas\xml;
class Attribute_vld_error_array extends DynamicAttribute{
	static function run(array $params = array(), $content = '') {
		if (! count ( $params )) {
			throw new Exception ( "AttrRuntimePlugin_vld_error: specificare almeno un parametro" );
		}

		$errorData = $params ["error"] ? $params ["error"] : $params [0];
		if (! is_array ( $errorData ) && ! ($errorData instanceof Traversable)) {
			return '';
		}
		$dom = new xml\XMLDom ();
		foreach ( $errorData as $index => $errori ) {
			// se il plugin viene chiamato n volte i messaggi vengono stampanti n volte, poso specificare params[index]
			if ($params ["index"] && $params ["index"]!=$index) continue;

			$root = $dom->addChildNS ( "Validation", "validation" )->setAttr ( "index", $index );
			//main
			if ($errori instanceof Exception) {
				$root->addChildNS ( "Validation", "main" )->setAttr ( "value", $errori->getMessage () );
			} elseif (is_array ( $errori ) && isset ( $errori ["MAIN"] )) {
				$root->addChildNS ( "Validation", "main" )->setAttr ( "value", $errori ["MAIN"] );
				unset ( $errori ["MAIN"] );
			}
			//mesages
			if (($errori instanceof Traversable) || is_array ( $errori )) {
				foreach ( $errori as $key => $val ) {
					$root->addChildNS ( "Validation", "message" )->setAttr ( "for", $key )->setAttr ( "value", $val );
				}
			}
		}
		$ret = '';
		foreach ( $dom->childNodes as $node ) {
			$ret .= $dom->saveXML ( $node );
		}
		return $ret;
	}
}
