<?php
namespace Goetas\Twital;

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
     * Array of callbacks that runs after DOM compilation process.
     * @return array
     */
    public function getPostFilters();

    /**
     * Array of callbacks that runs before DOM compilation process (after template loading).
     * @return array
     */
    public function getPreFilters();


    /**
     * Array of objects implementing {DOMLoader} interface, responsible of DOM loading from Twital ource code.
     * The key of
     * @return array
     */
    public function getLoaders();


    /**
     * Array of objects implementing {DOMDumper} interface, responsible of DOM dumping into Twig source code.
     * The key of
     * @return array
     */
    public function getDumpers();
}
