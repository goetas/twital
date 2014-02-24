Twig for Developers
===================

This chapter describes the API to Twital and not the template language.
It will be most useful as reference to those implementing the template interface to the application
and not those who are creating Twig templates.

Basics
------

Twital wraps a Twig instance and uses Twig to compile and run templates.

The first step is configure a valid Twig instance, then we can configure the
Twital object.

.. code-block:: php


    $loader = new Twig_Loader_Filesystem('/path/to/templates');
    $twig = new Twig_Environment($loader, array(
        'cache' => '/path/to/compilation_cache',
    ));

    $twital = new Twital($twig);


By default Twital comes with HTML5 tokenizer enabled. If you want to change it
to XHTML you can do it:

.. code-block:: php

    $twital = new Twital($twig, 'xhtml');


Other options avaiable are ``xml``, ``html5`` and ``xhtml``.


This will create a template environment with the default settings and a

To load a template just have to call the ``loadTemplate()`` method which then
returns a ``Twig_Template`` instance::

.. code-block:: php

    $template = $twital->loadTemplate('index.html.twital');

To render the template with some variables, call the ``render()`` method::


.. code-block:: php

    echo $template->render(array('the' => 'variables', 'go' => 'here'));

.. note::

    The ``display()`` method is a shortcut to output the template directly.

You can also load and render the template in one fell swoop::

.. code-block:: php

    echo $twital->render('index.html.twital', array('the' => 'variables', 'go' => 'here'));



Extending Twig
==============


As Twig, Twital is very extensible and you can hook into it.
The best way to extend Twital is create your own "extension" and provide
your functionalities.

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

Twig can be extended in many ways; you can add extra tags, filters, tests,
operators, global variables, and functions. You can even extend the parser
itself with node visitors.



Creating an Extension
---------------------

To create an extension you have to implement `Extension` interface or to extend the `AbstractExtension` class.

This is the `Extension` interface:

.. code-block:: php

    include ../../../src/Goetas/Twital/Extension.php


When createt our extension we have to add it to Twital  by using the ``addExtension()`` method on your
main Twital object::

    $twig = new Twig_Environment($loader);
    $twital = new TwitalEnviroment($twig);
    $twital->addExtension(new MyNewCustomExtension());


.. tip::

    The bundled extensions are great examples of how extensions work.
.. note::

    In some special cases you may need to create a Twig extension instead of Twital one.

Creating a `Node` parser
----------------------

To add your node parser, first you have to implement the `Node` class.


Suppose that we would to create an extension to handle an attribute that echoes "Hello world".

.. code-block:: xml
    <div class="red" xmlns:my="http://www.example.com/namespace">
        <my:hello name="John"/>
    </div>

The 'Node` class can be something like this:

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


* ``$node``: Gets the the DOM node for our tag.

* ``$twital``: Gets the Twital compiler.

No return value for `visit` method will be required.

`$compiler->applyTemplatesToChilds` or `$compiler->applyTemplates` or `$compiler->applyTemplatesToAttributes`
can be very useful when need to process also the content of node.

Finaly you have to create your extension that ships your node parser.


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

As you can see, the `getNodes` method have to return a two-level hash.
* The first level is the node namespace
* The second level is the node name

Of course, an extension can ship nodes that works with multiple namespaces.

Creating a `Attribute` parser
----------------------

To add your attribute parser, first you have to implement the `Attribute` class.


Suppose that we would to create an extension to handle an attribute that simply appends some text inisde a node,
 remving its original content.

.. code-block:: xml
    <div class="red" xmlns:my="http://www.example.com/namespace">
       <p my:replace="rawHtmlVar">
        This text will be replaced with the content of "rawHtmlVar" variable.
        </p>
    </div>

The 'Node` class can be something like this:

.. code-block::
    class HelloNode implements Attribute
    {
        function visit(\DOMAttr $attr, Compiler $twital)
        {

            $printNode = $twital->createPrintNode($attr->ownerNode->ownerDocument, $attr." | raw");

            $attr->ownerNode->appendChild($printNode);
            $node->parentNode->insertBefore($helloNode, $nameNode);

            return Attribute::STOP_NODE;
        }
    }


* ``$attr``: Gets the the `DOMAttr` node for our attribute.

* ``$twital``: Gets the Twital compiler.

The `visit` method can also return one of the following constants:
* `Attribute::STOP_NODE` : instructs the compiler to skip to next node (next sibiling)
* `Attribute::STOP_ATTRIBUTE` : instructs  the compiler to stop processing attributes of current node

Finaly you have to create your extension that ships your node parser.

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

As you can see, the `getNodes` method have to return a two-level hash.
* The first level is the node namespace
* The second level is the node name
Crating a `preFilter`
---------------------

Since Twital works internaly with DOMDocument, any template must be transformed into it.

Sometimes, the input tempalte is not completley XML (`DOMDocument` compatible), so you have do adapt it.

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

This realy simple extension repalces, just befor compilation phase, all occurences of "foo" with "bar" from the input template.

.. note::
    To see some examples of possible pre-filters please look at the source

Crating a `postFilter`
---------------------

Since Twital works internaly with `DOMDocument` that outputs out only XML, if you need to output a template in a different format
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

Also this is a really simple extension that repalces, just befor evaluation/saving phase, all occurences of "foo" with "bar" from the input template.

.. note::
    To see some examples of possible post-filters please look at the source

Creating a DOM `Loader`
----------------------

Since Twital works internaly with DOMDocument, any template must be transformed into it.

To create a "loader" you have to implement  the `Loader` interface.

If a source code is XML your loader can be something like this;

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

As you can see, the `getNodes` method have to return a hash.
The key is used to select the right loader.

Creating a DOM `Dumper`
----------------------

Since Twital works internaly with DOMDocument,
any template must be transformed into raw stream after compilation phase.

To create a "dumper" you have to implement  the `Dumper` interface.

To dump directly into XML, your dumper might look like this;

.. code-block:: php

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
            if ($metedata['xmldeclaration']) {
                return $dom->saveXML();
            } else {
                $cnt = array();

                foreach ($dom->childNodes as $node) {
                    $cnt[] = $dom->saveXML($node);
                }
                return implode("", $cnt);
            }
        }
    }
* ``collectMetadata()`` method can collect some data from orignak document (before DOM loading)
** `$dom` contains the *Dom* just after DOM loading
** `$original` contains the original template content

* ``dump()`` method dump a *DOM* into a string
** `$dom` contains the `DOMDocument`
** `$metadata` contains the metadatas collected by  `collectMetadata` method
* ``$xml``: Gets the raw template content

Finaly you have to create your extension that ships your loader.


.. code-block::
    class MyExtension extends AbstractExtension
    {
        public function getLoaders()
        {
            return array(
                'xml'=>new XMLLoader()
            );
        }
    }

As you can see, the `getNodes` method have to return a hash.
The key is used to select the right loader.