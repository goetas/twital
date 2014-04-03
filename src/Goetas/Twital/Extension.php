<?php
namespace Goetas\Twital;

/**
 *
 * @author Asmir Mustafic <goetas@gmail.com>
 *
 */
interface Extension
{
    /**
     * Array of objects implementing Node interface, responsible of DOM node handling.
     * The returned array must be a "two level array", first level as namespace and second level as attribute name.
     * Example:
     * <code>
     * 	array(
     * 		'http://www.w3.org/1998/Math/MathML'=>array(
     * 			'math'=>MathML\MathAttribute()
     * 		)
     *  )
     * </code>
     *
     * @return array
     */
    public function getAttributes();

    /**
     * Array of objects implementing Node interface, responsible of DOM node handling.
     * The returned array must be a "two level array", first level as namespace and second level as attribute name.
     * Example:
     * <code>
     * 	array(
     * 		'http://www.w3.org/1998/Math/MathML'=>array(
     * 			'math'=>MathML\MathAttribute()
     * 		)
     *  )
     * </code>
     *
     * @return array
     */
    public function getNodes();

    /**
     * Array of event subscribers
     * @return array
     */
    public function getSubscribers();

}