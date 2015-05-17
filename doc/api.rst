Twital for Developers
=====================

This chapter describes the PHP API to Twital and not the template language.
It is mostly aimed to developers who want to integrate Twital in their projects.

Basics
------

Twital is a Twig Loader that pre-compiles some templates before sending 
them back to Twig, which compiles and runs the templates.

The first step to using Twital is to configure a valid Twig instance. Later, we can configure the
Twital object.

.. code-block:: php

    <?php
    use Goetas\Twital\TwitalLoader;

    $loader = new Twig_Loader_Filesystem('/path/to/templates');
    $twitalLoader = new TwitalLoader($loader);
    
    $twig = new Twig_Environment($twitalLoader, array(
        'cache' => '/path/to/compilation_cache',
    ));


By default, Twital compiles only templates whose name ends with `.twital.xml`, `.twital.html`, `.twital.xhtml`
(by using the right source adapter).
If you want to change it, adding more supported file formats, you can do something like this:

.. code-block:: php

    <?php
    
    $twital = new TwitalLoader($loader);
    $twital->addSourceAdapter('/\.wsdl$/', new XMLAdapter()); // handle .wsdl files as XML
    $twital->addSourceAdapter('/\.htm$/', new HTML5Adapter()); // handle .htm files as HTML5
    
.. note::

    Built in adapters are: `XMLAdapter`, `XHTMLAdapter` and `HTML5Adpater`.

.. note::

    To learn more about adapters, you can read the dedicated chapter :ref``Creating a SourceAdapter``.


Finally, to render a template with some variables, simply call the ``render()`` method on Twig instance:

.. code-block:: php

    <?php
    echo $twig->render('index.twital.html', array('the' => 'variables', 'go' => 'here'));


How does Twital work?
~~~~~~~~~~~~~~~~~~~~~

Twital uses Twig to render templates, but before passing a template to Twig,
Twital pre-compiles it in its own way.

The rendering of a template can be summarized into the following steps:

* **Load** the template (done by Twig): if the template has already been compiled, Twig loads it and goes
  to the *evaluation* step. Otherwise:
  
  * A `SourceAdapter` is chosen (from a set of configured adapters);
  * The **compiler.pre_load** event is fired; 
    Here, listeners can transform the template source code before DOM loading;
  * The `SourceAdapter` will `load` the source code into a valid DOMDocument_ object;
  * The **compiler.post_load** event is fired.
  * The compiler transforms recognized attributes and nodes into the relative Twig code;
  * The **compiler.pre_dump** event is fired.
  * The `SourceAdapter` will `dump` the compiled `DOMDocument` into Twig source code;
  * The **compiler.post_dump** event is fired.
    Here, listeners can perform some non-DOM transformations to the new template source code;
  * Twital passes the final source code to Twig (Finally Twig compiles the Twig source code into PHP code) 
* **Evaluate** the template: Twig calls the ``display()`` method of the compiled template by passing a context.



Extending Twital
----------------

As Twig, Twital is very extensible and you can hook into it.
The best way to extend Twital is to create your own "extension" and provide
your functionalities.


Creating a `SourceAdapter`
~~~~~~~~~~~~~~~~~~~~~~~~~~

Source adapters adapt a resource representation (usually a file or a string) 
to something that can be converted into a PHP `DOMDocument`_ object. 
Note that, the same object has to be "re-adapted" into its original representation.

If you want to provide a source adapter, there is no need to create an extension;
you can simply implement the ``Goetas\Twital\SourceAdapter`` interface and use it.

.. literalinclude:: ../src/Goetas/Twital/SourceAdapter.php
   :language: php


To enable an adapter, you have to add it to Twital's loader instance by using the ``addSourceAdapter()`` method:

.. code-block:: php

    <?php
    use Goetas\Twital\TwitalLoader;
    
    $twital = new TwitalLoader($fileLoader);
    $twital->addSourceAdapter('/.*.xml$/i', new MyXMLAdapter());


A "naive" implementation of `MyXMLAdapter` can be:

.. code-block:: php

    <?php
    use Goetas\Twital\SourceAdapter;
    use Goetas\Twital\Template;
    
    class MyXMLAdapter implements SourceAdapter
    {
        public function load($source)
        {
            $dom = new \DOMDocument('1.0', 'UTF-8');
            $someMetadata = null; // you can also extract some metadata from original source
            
            return new Template($dom, $someMetadata);
        }
        
        public function dump(Template $template)
        {
            $metedata = $template->getMetadata();
            $dom = $template->getDocument();
    
            return $dom->saveXML();
        }
    }
 
- As you can see, ``load`` takes a string (containing the Twital template source code), and returns a ``Goetas\Twital\Template`` object.
 - ``Goetas\Twital\Template`` is an object that requires a `DOMDocument`_ as first argument and a generic variable as second argument (useful to hold some metadata extracted from the original source, which can be used later during the "dump" phase).

- The ``dump`` method takes a ``Goetas\Twital\Template`` instance and returns a string. The returned string contains the template source code that will be passed to Twig.

Creating an `Extension`
~~~~~~~~~~~~~~~~~~~~~~~

An extension is simply a container of functionalities that can be added to Twital.
The functionalities are node parsers, attribute parses and generic event listeners.

To create an extension, you have to implement the ``Goetas\Twital\Extension`` interface or extend the `Goetas\Twital\Extension\AbstractExtension` class.

This is the ``Goetas\Twital\Extension`` interface:

.. literalinclude:: ../src/Goetas/Twital/Extension.php
   :language: php


To enable your extensions, you have to add them to your Twital instance by using the ``Goetas\Twital\Twital::addExtension()`` method:

.. code-block:: php

    <?php
    use Goetas\Twital\Twital;
    use Goetas\Twital\TwitalLoader;
    
    $twital = new Twital($twig);
    $twital->addExtension(new MyNewCustomExtension());
    
    $fsLoader = new Twig_Loader_Filesystem('/path/to/templates');
    $twitalLoader = new TwitalLoader($fsLoader, $twital);
    

.. tip::

    The bundled extensions are great examples of how extensions work.

.. note::

    In some special cases you may need to create a Twig extension instead of a Twital one.
    To learn how to create a Twig extension, you can read the `Twig official documentation <http://twig.sensiolabs.org/doc/advanced.html>`_

Creating a `Node` parser
~~~~~~~~~~~~~~~~~~~~~~~~

Node parsers are aimed at handling any custom XML/HTML tag.

Suppose that you want to create an extension to handle a tag ``<my:hello>`` that simply prints `"Hello {name}"`:

.. code-block:: xml

    <div class="red" xmlns:my="http://www.example.com/namespace">
        <my:hello name="John"/>
    </div>


First, you have to create your node parser, which handles this "new" tag. 
To do this, you have to implement the ``Goetas\Twital\Node`` interface.

.. literalinclude:: ../src/Goetas/Twital/Node.php
   :language: php
   

The ``HelloNode`` class can be something like this:

.. code-block:: php

    <?php
    use Goetas\Twital\Node;
    use Goetas\Twital\Compiler;
    
    class HelloNode implements Node
    {
        function visit(\DOMElement $node, Compiler $twital)
        {
            $helloNode = $node->ownerDocument->createTextNode("hello");
            $nameNode = $twital->createPrintNode(
               $node->ownerDocument,
               "'".$node->getAttribute("name")."'"
            );

            $node->parentNode->replaceChild($nameNode, $node);
            $node->parentNode->insertBefore($helloNode, $nameNode);
        }
    }

Let's take a look at the ``Goetas\Twital\Node::visit`` method signature:

- ``$node`` gets the `DOMElement`_ node of your ``my:hello`` tag;
- ``$twital`` gets the Twital compiler;
- No return value for the ``visit`` method is required.

The aim of the ``Goetas\Twital\Node::visit`` method is to transform the Twital template representation into the Twig template syntax.

.. tip::

    ``$compiler->applyTemplatesToChilds()``, ``$compiler->applyTemplates()`` or ``$compiler->applyTemplatesToAttributes()``
    can be very useful when you need to process recursively the content of a node.

Finally, you have to create an extension that ships your node parser.

.. code-block:: php

    <?php
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

As you can see, the ``getNodes`` method has to return a two-level hash.

- The first level is the node namespace;
- The second level is the node name.

Of course, an extension can ship nodes that work with multiple namespaces.

.. tip::
	
	To make the ``xmlns:my`` declaration optional, you can also use the event listener as ``Goetas\Twital\EventSubscriber\CustomNamespaceRawSubscriber``.

Creating an `Attribute` parser
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

An attribute parser aims at handling custom XML/HTML attributes.

Suppose that we want to create an extension to handle an attribute that simply appends some text inside a node,
removing its original content.

.. code-block:: xml

    <div class="red" xmlns:my="http://www.example.com/namespace">
        <p my:replace="rawHtmlVar">
            This text will be replaced with the content of the "rawHtmlVar" variable.
        </p>
    </div>

To add your attribute parser, first you have to implement the ``Goetas\Twital\Attribute`` interface.

.. literalinclude:: ../src/Goetas/Twital/Attribute.php
   :language: php

The ``HelloAttribute`` class can be something like this:

.. code-block:: php

    <?php
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

Let's take a look at the ``Goetas\Twital\Attribute::visit`` method:

- ``$attr`` gets the `DOMAttr` node of your attribute;
- ``$twital`` gets the Twital compiler.

The ``visit`` method has to transform the custom attribute into a valid Twig code.

The ``visit`` method can also return one of the following constants:

- ``Attribute::STOP_NODE``: instructs the compiler to jump to the next node (go to next sibling), stopping the processing of possible node childs;
- ``Attribute::STOP_ATTRIBUTE``: instructs the compiler to stop processing attributes of the current node (continue normally with child and sibling nodes).

Finally, you have to create an extension that ships your attribute parser.

.. code-block:: php

    <?php
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

As you can see, the ``getAttributes`` method has to return a two-level hash.
- The first level is the attribute namespace;
- The second level is the attribute name.

Of course, an extension can ship nodes that work with multiple namespaces.

.. tip::
	
	To make the ``xmlns:my`` declaration optional, you can also use the event listener as ``Goetas\Twital\EventSubscriber\CustomNamespaceRawSubscriber``.


Event Listeners
~~~~~~~~~~~~~~~

Another convenient way to hook into Twital is to create an event listener.

The possible entry points for listeners are:

- **compiler.pre_load**, fired before the source has been passed to the source adapter; 
- **compiler.post_load**, fired after the source has been loaded into a DOMDocument;
- **compiler.pre_dump**, fired before the DOMDocument has been passed to the source adapter for the dumping phase;
- **compiler.post_dump**, fired after the DOMDocument has been dumped into a string by the source adapter.


A valid listener must implement the ``Symfony\Component\EventDispatcher\EventSubscriberInterface`` interface.

This is an example for a valid listener:

.. code-block:: php

    <?php
    class MySubscriber implements EventSubscriberInterface {
        public static function getSubscribedEvents() {
            return array(
                'compiler.post_dump' => 'modifySource'                
                'compiler.pre_dump' => 'modifyDOM'
                
                'compiler.post_load' => 'modifyDOM',
                'compiler.pre_load' => 'modifySource'
            );
        }
        public function modifyDOM(TemplateEvent $event) {
            $event->getTemplate(); // do something with template (returns a Template instance)
        }
        public function modifySource(SourceEvent $event) {
            $event->getTemplate(); // do something with template (returns a string)
        }
    }
    

Event ``compiler.pre_load``
...........................

This event is fired just before a `SourceAdapter` tries to load the source code into a `DOMDocument`_.
Here you can modify the source code, adapting it for a source adapter.

Here an example:

.. code-block:: php

    <?php
    class MySubscriber implements EventSubscriberInterface
    {
        public static function getSubscribedEvents(){
            return array(
                'compiler.pre_load' => 'modifySource'
            );
        }
        public function modifySource(SourceEvent $event) {
            $str = $event->getTemplate();
            $str = str_replace("&nbsp;", "&#160;", $str);
            
            $event->setTemplate($str);
        }
    }


.. tip::
    
    Take a look at ``Goetas\Twital\EventSubscriber\CustomNamespaceRawSubscriber`` to see what can be done using this event.

Event ``compiler.post_load``
............................

This event is fired just after a ``Goetas\Twital\SourceAdapeter::load()`` call.
Here you can modify the `DOMDocument`_ object; it is a good point where to apply modifications 
that can't be done by node parsers. 
You can also add nodes that will be parsed by Twital (eg: ``t:if`` attribute, ``t:include`` nodes, etc). 

Here an example:

.. code-block:: php

    <?php
    class MySubscriber implements EventSubscriberInterface {
        public static function getSubscribedEvents() {
            return array(
                'compiler.post_load' => 'modifyDOM'
            );
        }
        public function modifyDOM(TemplateEvent $event) {
            $template = $event->getTemplate();
            $dom = $template->getTemplate();
            
            $nodes = $dom->getElementsByTagName('mynode');
            
            // do something with $nodes
        }
    }

.. tip::
    
    Take a look at ``Goetas\Twital\EventSubscriber\CustomNamespaceSubscriber`` to see what can be done using this event.


Event ``compiler.pre_dump``
...........................

This event is fired when the Twital compilation process ends. 
It is similar to the ``compiler.post_load`` event, but
you can not add elements that need to be parsed by Twital.

Here an example:

.. code-block:: php

    <?php
    class MySubscriber implements EventSubscriberInterface
    {
        public static function getSubscribedEvents(){
            return array(
                'compiler.pre_dump' => 'modifyDOM'
            );
        }
        public function modifyDOM(TemplateEvent $event) {
            $template = $event->getTemplate();
            $dom = $template->getTemplate();
            
            $body = $dom->getElementsByTagName('body')->item(0);
            // do something with body node...
        }
    }


Event ``compiler.post_dump``
............................

This event is fired just after the ``Goetas\Twital\SourceAdapeter::dump()`` call. 
Here you can modify the final source code, which will be passed to Twig. 

Here an example:

.. code-block:: php

    <?php
    class MySubscriber implements EventSubscriberInterface {
        public static function getSubscribedEvents() {
            return array(
                'compiler.post_dump' => 'modifySource'
            );
        }
        public function modifySource(SourceEvent $event) {
            $str = $event->getTemplate();
            $str.=" {# generated by Twital #}";
            
            $event->setTemplate($str);
        }
    }

    
.. tip::
    
    Take a look at ``Goetas\Twital\EventSubscriber\DOMMessSubscriber`` to see what can be done using this event.

Ship your listeners
...................
    
If you have created your listeners, add them to Tiwtal.
To do this, you have to create an extension that ships your listeners.

.. code-block:: php

    <?php
    class MyExtension extends AbstractExtension
    {
      public function getSubscribers()
      {
         return array(
             new MySubscriber(),
             new MyNewSubscriber()
         );
      }
    }    
    
    
    
.. _DOMDocument: http://www.php.net/manual/en/class.domdocument.php
