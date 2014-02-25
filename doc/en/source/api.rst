Twital for Developers
===================

This chapter describes the PHP API to Twital and not the template language.
It will be most useful as reference to those implementing the template interface to the application
and not those who are creating Twig templates.

Basics Usage
------------

Twital wraps a Twig instance and uses Twig to compile and run templates.

The first step is configure a valid Twig instance, then we can configure the
Twital object.

.. code-block:: php

    use Goetas\Twital\Twital;

    $loader = new Twig_Loader_Filesystem('/path/to/templates');
    $twig = new Twig_Environment($loader, array(
        'cache' => '/path/to/compilation_cache',
    ));

    $twital = new Twital($twig);


By default Twital comes with HTML5 tokenizer enabled.
To change it you can do:

.. code-block:: php

    $twital = new Twital($twig, 'xhtml');


Other options available are ``xml``, ``html5`` and ``xhtml``.


By default Twital will compile only templates that contains ``.twital`` in their file name.
If you want to change it:

.. code-block:: php

    $twital = new Twital($twig, array());
    $twital->addFileNamePattern('\.xml$'); // filename regex
    $twital->addFileNamePattern(function($name){
        return strpos($name, 'foo')!==false;
    }); // callback


To render the template with some variables, call the ``render()`` method::


.. code-block:: php

    echo $twital->render('index.twital.html', array('the' => 'variables', 'go' => 'here'));


How does Twig work?
-------------------

Twital uses Twig to redener templates, but before pass a template to Twig,
Twital pre-compiles it in its own way.

The rendering of a  template can be summarized into this steps:

* **Load** the template (done by Twig): If the template is already compiled, load it and go
  to the *evaluation* step, otherwise:

  * First, the **PreFilters** callbacks can transform the template source code before DOM loading;
  * Second, the **DOMLoader** transform the source code into a valid DOMDocument object;
  * Third, the compiler transform the recognized attributes and nodes into relative Twig code;
  * Fourth, the **DOMDumper** transform the compiled new DOMDocument into Twig source code;
  * Fifth, The **PostFilters** callbacks can transform the new template source code before send it to Twig;
  * Sixth, pass the template source code to Twig:
      * First, the **lexer** tokenizes the template source code into small pieces
        for easier processing;
      * Then, the **parser** converts the token stream into a meaningful tree
        of nodes (the Abstract Syntax Tree);
      * Eventually, the *compiler* transforms the AST into PHP code.

* **Evaluate** the template  (done by Twig): It basically means calling the ``display()``
  method of the compiled template and passing it the context.



Extending Twig
--------------

As Twig, Twital is very extensible and you can hook into it.
The best way to extend Twital is create your own "extension" and provide
your functionalities.


Creating an Extension
~~~~~~~~~~~~~~~~~~~~~

To create an extension you have to implement ``Goetas\Twital\Extension`` interface or extend the `Goetas\Twital\Extension\AbstractExtension` class.

This is the ``Goetas\Twital\Extension`` interface:

.. literalinclude:: ../../../src/Goetas/Twital/Extension.php
   :language: php


To enable our extension, we have to add it to Twital's instance by using the ``addExtension()`` method:

.. code-block:: php

    $twital = new Twital($twig);
    $twital->addExtension(new MyNewCustomExtension());


.. tip::

    The bundled extensions are great examples of how extensions work.

.. note::

    In some special cases you may need to create a Twig extension instead of Twital one.
    To learn how to create a Twig extension you can read the  `Twig official documentation <http://twig.sensiolabs.org/doc/advanced.html>`_

Creating a `Node` parser
~~~~~~~~~~~~~~~~~~~~~~~~

Node parser is aimed to handle custom XML/HTML tags.



Suppose that we would to create an extension to handle a tag ``<my:hello>`` that echoes "Hello world".

.. code-block:: xml
    <div class="red" xmlns:my="http://www.example.com/namespace">
        <my:hello name="John"/>
    </div>


To create your node parser that handles this "new" tag, you have to implement the `'Goetas\Twital\Node`` interface.

The ``HelloNode`` class can be something like this:

.. code-block::
    class HelloNode implements Node
    {
        function visit(\DOMElement $node, Compiler $twital)
        {
            $helloNode = $node->ownerDocument->createTextNode("hello");
            $nameNode = $twital->createPrintNode($node->ownerDocument, "'".$node->getAttribute("name")."'");

            $node->parentNode->replaceChild($nameNode, $node);
            $node->parentNode->insertBefore($helloNode, $nameNode);
        }
    }

Let's take a look to ``visit`` method:

* ``$node``: Gets the the DOM node relative to our ``my:hello`` tag.
* ``$twital``: Gets the Twital compiler.
* No return value for `visit` method will be required.

``visit`` method have to transform the Twital template representation into Twig template syntax.
`$compiler->applyTemplatesToChilds` or `$compiler->applyTemplates` or `$compiler->applyTemplatesToAttributes`
can be very useful when need to process also the content of node.

Finally you have to create your extension that ships your node parser.

.. code-block::
    class MyExtension extends AbstractExtension
    {
        public function getNodes()
        {
            return array(
                'http://www.example.com/namespace'=>array(
                    'hello' => new HelloNode()
                )
            );
        }
    }

As you can see, the ``getNodes`` method have to return a two-level hash.
* The first level is the node namespace
* The second level is the node name

Of course, an extension can ship nodes that works with multiple namespaces.


Creating a `Attribute` parser
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Attribute parser is aimed to handle custom XML/HTML attributes.

Suppose that we would to create an extension to handle an attribute that simply appends some text inside a node,
removing its original content.

.. code-block:: xml
    <div class="red" xmlns:my="http://www.example.com/namespace">
        <p my:replace="rawHtmlVar">
            This text will be replaced with the content of "rawHtmlVar" variable.
        </p>
    </div>

To add your attribute parser, first you have to implement the ``Goetas\Twital\Attribute`` interface.


The ``HelloAttribute`` class can be something like this:

.. code-block::
    class HelloAttribute implements Attribute
    {
        function visit(\DOMAttr $attr, Compiler $twital)
        {
            $printNode = $twital->createPrintNode($attr->ownerNode->ownerDocument, $attr." | raw");

            $attr->ownerNode->appendChild($printNode);
            $node->parentNode->insertBefore($helloNode, $nameNode);

            return Attribute::STOP_NODE;
        }
    }

Let's take a look to ``visit`` method:
* ``$attr``: Gets the the `DOMAttr` node for our attribute.
* ``$twital``: Gets the Twital compiler.

The ``visit`` method have to transform the custom attribute into valid Twig code.

The ``visit`` method can also return one of the following constants:
* ``Attribute::STOP_NODE``: instructs the compiler to skip to next node (go to next sibling)
* ``Attribute::STOP_ATTRIBUTE``: instructs the compiler to stop processing attributes of current node (continues with child and sibling nodes)

Finally you have to create your extension that ships your attribute parser.

.. code-block::
    class MyExtension extends AbstractExtension
    {
        public function getAttributes()
        {
            return array(
                'http://www.example.com/namespace'=>array(
                    'replace' => new HelloAttribute()
                )
            );
        }
    }

As you can see, the ``getAttributes`` method have to return a two-level hash.
* The first level is the node namespace
* The second level is the node name

Crating a `preFilter`
~~~~~~~~~~~~~~~~~~~~

Since Twital works internally with `DOMDocument <http://php.net/domdocument>`_,
any template must be transformed into it.

Sometimes, the input template is not completely XML (`DOMDocument`) compatible, so you have do adapt it
using a `preFilter`.

.. code-block::

    class MyExtension extends AbstractExtension
    {
        public function getPreFilters()
        {
            return array(
                function($input){
                    return str_replace("foo", "bar", $input);
                }
            );
        }
    }

This really simple extension replaces,  all occurrences of "foo" with "bar" contained into the input template.
This phase is done just before compilation process starts.

.. note::
    To see some examples of possible pre-filters please look at the source code

Crating a `postFilter`
~~~~~~~~~~~~~~~~~~~~~~

Since Twital works internally with `DOMDocument <http://php.net/domdocument>`_,
any template must be transformed into it. If you need to output a template in a different format
you have to adapt it (eg HTML).

.. code-block::

    class MyExtension extends AbstractExtension
    {
        public function getPostFilters()
        {
            return array(
                function($outputTemplate){
                    return str_replace("foo", "bar", $outputTemplate);
                }
            );
        }
    }

Also this is a really simple extension that replaced, all occurrences of "foo" with "bar".
This phase is done when all compilation phases are done.

.. note::
    To see some examples of possible post-filters please look at the source code

Creating a DOM `Loader`
~~~~~~~~~~~~~~~~~~~~~~~

Since Twital works internally with `DOMDocument <http://php.net/domdocument>`_,
any template must be transformed into it.

To create your own loader you have to implement the ``Goetas\Twital\Loader`` interface.

If your source code is in XML,  your loader can be something like this;

.. code-block:: php

    class XMLLoader implements Loader
    {
        public function load($xml)
        {
            $dom = new \DOMDocument('1.0', 'UTF-8');
            $dom->loadXML($xml);
            return $dom;
        }
    }

* ``$xml``: Gets the raw template content
* ``load`` have to return a ``DOMDocument`` object.


Finaly you have to create your extension that ships your loader.

.. code-block:: php

    class MyExtension extends AbstractExtension
    {
        public function getLoaders()
        {
            return array(
                'xml'=>new XMLLoader()
            );
        }
    }

As you can see, the ``getLoaders`` method have to return a hash.
The key is used to select the right loader.

.. note::
    Twital already comes with `xml`, `xhtml`, `html`, `html5` loaders

Creating a DOM `Dumper`
~~~~~~~~~~~~~~~~~~~~~~
Since Twital works internally with `DOMDocument <http://php.net/domdocument>`_,
any template must be transformed into it, and later re-transformed into a raw string.


If want to output your templates in XML, you have to
create a "dumper" that  implements  the ``Goetas\Twital\Dumper`` interface.


To dump directly into XML, your dumper might look like this;

.. code-block:: html+php

    class XMLDumper implements Dumper
    {
        public function collectMetadata(\DOMDocument $dom, $original)
        {
            $metedata = array();
            $metedata['xmldeclaration'] = strpos(rtrim($original), '<?xml ') === 0;
            $metedata['doctype'] = ! ! $dom->doctype;
            return $metedata;
        }

        public function dump(\DOMDocument $dom, $metedata)
        {
            return $dom->saveXML();
        }
    }

Let's thake a look to the class implementation:
- ``collectMetadata()`` method can collect some data from orignak document (before DOM loading)
 - `$dom` contains the *Dom* just after DOM loading
 - `$original` contains the original template content
- ``dump()`` method dump a *DOM* into a string
 - `$dom` contains the `DOMDocument`
 - `$metadata` contains the metadatas collected by  `collectMetadata` method
- ``$xml``: Gets the raw template content


Finaly you have to create your extension that ships your dumper.

.. code-block::

    class MyExtension extends AbstractExtension
    {
        public function getDumpers()
        {
            return array(
                'xml'=>new XMLDumper()
            );
        }
    }

As you can see, the ``getDumpers`` method have to return a hash.
The key is used to select the right dumper during the output phase.

.. note::
    Twital already comes with `xml`, `xhtml`, `html`, `html5` dumpers