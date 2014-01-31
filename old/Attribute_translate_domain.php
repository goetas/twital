<?php
/**
 * Traduce gli attributi di un elemento
 * ES:
 * &lt;img xmlns:t="Twital" title="eventi" t:translate-attr="title"/&gt;
 * si possono tradurre più attributi allo stesso tempo e si possono specificare più variabili per ogni attributo
 * ES:
 * &lt;img xmlns:t="Twital" alt="eventi del %periodo" title="eventi dell'%anno" t:translate-attr="title(anno='2009';mese='10');alt(periodo='10-2009')"/&gt;
 * si possono applicare dei modificatori ai valori delle variabili degli attributi, basta dividere le espressioni con le parentesi tonde
 * ES:
 * &lt;img xmlns:t="Twital" title="eventi dell'%anno" t:translate-attr="title(anno=('2009'|modificatore_generico))"/&gt;
 */
namespace goetas\atal\attribute;
use goetas\xml;
use goetas\atal\Attribute;
class Attribute_translate_domain extends Attribute {
	function start(xml\XMLDomElement $node, \DOMAttr $att) {

	}
}