<?php
namespace goetas\atal\attribute;
use Traversable;
use Exception;
use goetas\xml;
use goetas\atal\DynamicAttribute;
use goetas\atal\Twital;
class Attribute_vld_error extends DynamicAttribute {
	protected function getOptions() {
		return array('preserveContent'=>true);
	}
	static function run(array $params, $content) {

		if (! count ( $params )) {
			throw new Exception ( "AttrRuntimePlugin_vld_error: specificare almeno un parametro" );
		}

		$errorData = $params ["error"] ? $params ["error"] : $params [0];

		$dom = new xml\XMLDom ();
		$root = $dom->addChildNS ( "Validation", "validation" );

		if (isset ( $params ["index"] )) {
			$root->setAttr ( "index", $params ["index"] );
		} else {
			$root->setAttr ( "index", "__MAIN" );
		}
		if ($errorData instanceof Exception) {
			$root->addChildNS ( "Validation", "main" )->setAttr ( "value", $errorData->getMessage () );
		}
		if (is_array ( $errorData ) && isset ( $errorData ["MAIN"] )) {
			$root->addChildNS ( "Validation", "main" )->setAttr ( "value", $errorData ["MAIN"] );
			unset ( $errorData ["MAIN"] );
		}
		if (($errorData instanceof Traversable) || is_array ( $errorData )) {
			foreach ( $errorData as $key => $val ) {
				$root->addChildNS ( "Validation", "message" )->setAttr ( "for", $key )->setAttr ( "value", $val );
			}
		}

		return $dom->saveXML ( $root );
	}
}
