<?php
namespace Goetas\Twital\Tests\Attribute;

use Goetas\Twital\Tests\AbstractAttributeTest;
use Goetas\Twital\Attribute\BaseAttribute;
/**
 * OmitAttribute test case.
 */
class IfAttributeTest extends AbstractAttributeTest
{
	protected function getAttribute()
    {
        return new BaseAttribute();
    }

	protected function getData()
    {
        return array(
        	array('test', '{% if test %}<div>content</div>{% endif %}'),
            //array('true', '<div>content</div>')
        );

    }

	protected function getTagName()
    {
        return 'if';

    }



}

